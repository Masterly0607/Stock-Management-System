<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdjustmentService
{
  public function __construct(private LedgerWriter $ledger) {}

  public function confirm($adjustment): void
  {
    DB::transaction(function () use ($adjustment) {
      foreach ($adjustment->items as $item) { // rename to ->lines if your relation uses 'lines'
        $delta = (float) $item->qty_delta;
        if ($delta == 0.0) continue;

        $movement = $delta > 0 ? 'ADJUST_IN' : 'ADJUST_OUT';

        $this->ledger->post([
          'product_id' => $item->product_id,
          'branch_id' => $adjustment->branch_id,
          'qty' => abs($delta),
          'movement' => $movement,
          'source_type' => 'adjustments',
          'source_id' => $adjustment->id,
          'source_line' => $item->id,
        ]);
      }

      if (method_exists($adjustment, 'update')) {
        $adjustment->update(['status' => 'POSTED', 'posted_at' => now()]);
      }
    });
  }
}
