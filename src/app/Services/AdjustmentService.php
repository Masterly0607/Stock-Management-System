<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use DomainException;

class AdjustmentService
{
  public function __construct(protected LedgerWriter $ledger) {}

  public function post(\App\Models\Adjustment $adjustment, ?int $userId = null): \App\Models\Adjustment
  {
    if ($adjustment->status !== 'DRAFT') {
      throw new DomainException('Only DRAFT adjustments can be posted.');
    }
    if ($adjustment->posted_at) {
      throw new DomainException('Adjustment already posted.');
    }

    $items = DB::table('adjustment_items')
      ->where('adjustment_id', $adjustment->id)
      ->get();

    if ($items->isEmpty()) {
      throw new DomainException('Adjustment has no items.');
    }

    // Determine stock column name
    $qtyCol = Schema::hasColumn('stock_levels', 'on_hand') ? 'on_hand'
      : (Schema::hasColumn('stock_levels', 'qty') ? 'qty'
        : (Schema::hasColumn('stock_levels', 'quantity') ? 'quantity' : null));
    if (!$qtyCol) {
      throw new DomainException('stock_levels has no quantity column (on_hand/qty/quantity).');
    }

    // Pre-check negatives
    foreach ($items as $it) {
      if ((float)$it->qty_delta < 0) {
        $where = [
          'branch_id'  => $adjustment->branch_id,
          'product_id' => $it->product_id,
        ];
        if (Schema::hasColumn('stock_levels', 'unit_id') && !is_null($it->unit_id)) {
          $where['unit_id'] = $it->unit_id;
        }
        $available = (float)(DB::table('stock_levels')->where($where)->value($qtyCol) ?? 0);
        if ($available + (float)$it->qty_delta < 0) {
          throw new DomainException("Insufficient stock for product {$it->product_id} on adjust-out.");
        }
      }
    }

    DB::transaction(function () use ($adjustment, $items, $userId) {
      foreach ($items as $it) {
        $delta = (float)$it->qty_delta;
        if ($delta == 0.0) continue;

        $payload = [
          'product_id'  => $it->product_id,
          'branch_id'   => $adjustment->branch_id,
          'qty'         => abs($delta),
          'movement'    => $delta >= 0 ? 'ADJ_IN' : 'ADJ_OUT',
          'source_type' => 'adjustments',
          'source_id'   => $adjustment->id,
          'source_line' => $it->id ?? 0,
        ];
        if (Schema::hasColumn('inventory_ledger', 'unit_id') && !is_null($it->unit_id)) {
          $payload['unit_id'] = $it->unit_id;
        }

        $this->ledger->post($payload);
      }

      DB::table('adjustments')->where('id', $adjustment->id)->update([
        'status'     => 'POSTED',
        'posted_at'  => now(),
        'approved_by' => $userId,
        'updated_at' => now(),
      ]);
    });

    return $adjustment->fresh();
  }
}
