<?php

namespace App\Services;

use App\Models\InventoryLedger;
use App\Models\StockLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LedgerWriter
{
  /**
   * Atomic, idempotent stock posting.
   * Required keys: product_id, branch_id, qty (>0), movement, source_type, source_id, (optional) source_line
   */
  public function post(array $payload): InventoryLedger
  {
    $payload['posted_at']  = $payload['posted_at']  ?? now();
    $payload['source_line'] = $payload['source_line'] ?? 0;
    $payload['posted_by']  = $payload['posted_by']  ?? optional(Auth::user())->id;

    return DB::transaction(function () use ($payload) {
      // 1) Idempotency (prevent double posting)
      $exists = InventoryLedger::query()->where([
        'source_type' => $payload['source_type'],
        'source_id'   => $payload['source_id'],
        'source_line' => $payload['source_line'],
        'branch_id'   => $payload['branch_id'],
        'product_id'  => $payload['product_id'],
        'movement'    => $payload['movement'],
      ])->lock('share')->exists();

      if ($exists) {
        throw ValidationException::withMessages([
          'posting' => 'This movement was already posted.'
        ]);
      }

      // 2) Lock stock row (create if not exists)
      $level = StockLevel::query()
        ->where('branch_id', $payload['branch_id'])
        ->where('product_id', $payload['product_id'])
        ->lockForUpdate()
        ->first();

      if (!$level) {
        $level = new StockLevel([
          'branch_id'  => $payload['branch_id'],
          'product_id' => $payload['product_id'],
          'qty'        => 0,
        ]);
        $level->save();
      }

      $qty = (float) ($payload['qty'] ?? 0);
      if ($qty <= 0) {
        throw ValidationException::withMessages(['qty' => 'Quantity must be greater than 0.']);
      }

      // 3) Compute new balance (and prevent negative)
      $movement = $payload['movement'];
      $isOut = str_contains(strtoupper($movement), 'OUT');
      $newBalance = $isOut ? $level->qty - $qty : $level->qty + $qty;

      if ($newBalance < 0) {
        throw ValidationException::withMessages(['stock' => 'Insufficient stock for this operation.']);
      }

      // 4) Write ledger row
      $ledger = new InventoryLedger([
        'product_id'    => $payload['product_id'],
        'branch_id'     => $payload['branch_id'],
        'movement'      => $movement,
        'qty'           => $qty,
        'balance_after' => $newBalance,
        'source_type'   => $payload['source_type'],
        'source_id'     => $payload['source_id'],
        'source_line'   => $payload['source_line'],
        'posted_at'     => $payload['posted_at'],
        'posted_by'     => $payload['posted_by'],
        'hash'          => $this->hash($payload, $newBalance),
      ]);
      $ledger->save();

      // 5) Update balance snapshot
      $level->qty = $newBalance;
      $level->save();

      return $ledger;
    });
  }

  protected function hash(array $payload, float $balance): string
  {
    return hash('sha256', implode('|', [
      $payload['product_id'],
      $payload['branch_id'],
      strtoupper($payload['movement']),
      (string)$payload['qty'],
      $payload['source_type'],
      $payload['source_id'],
      $payload['source_line'],
      (string)$payload['posted_at'],
      (string)$balance,
    ]));
  }

  // Convenience helpers
  public function postIn(array $base): InventoryLedger
  {
    $base['movement'] = $base['movement'] ?? 'IN';
    return $this->post($base);
  }

  public function postOut(array $base): InventoryLedger
  {
    $base['movement'] = $base['movement'] ?? 'OUT';
    return $this->post($base);
  }
}
// What is a Service file in Laravel? => A Service file is a PHP class where you put important business logic(not just database CRUD, but rules — like “no negative stock” or “pay before deliver”).

// Why use a service file?
// 1. It keeps controllers clean (controllers just call the service).
// 2. It avoids repeating the same logic in many places.
// 3. It’s easier to test and maintain.

// Main goal of LedgerWriter Service: the brain that controls your stock.  It does 5 main things every time you call it:
// 1. Updates the stock quantity (stock_levels table)
// 2. Writes a history record (inventory_ledger table)
// 3. Prevents negative stock
// 4. Prevents duplicate entries
// 5. Uses a safe transaction (no partial updates if something fails)
