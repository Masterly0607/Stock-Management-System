<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoScenarioSeeder extends Seeder
{
    public function run(): void
    {
        // Lookup IDs that previous seeders created
        $hq        = DB::table('branches')->where('code', 'BR-HQ')->value('id');
        $pp        = DB::table('branches')->where('code', 'BR-PP')->value('id');
        $dp        = DB::table('branches')->where('code', 'BR-DNP')->value('id');
        $userSuper = DB::table('users')->where('email', 'super@hq.local')->value('id');
        $userAdmin = DB::table('users')->where('email', 'admin.pp@hq.local')->value('id');
        $userDist  = DB::table('users')->where('email', 'dist.dp@hq.local')->value('id');

        // Demo product/unit
        $prod = 101; // Shampoo 250ml
        $unit = 1;   // pcs

        // 1) PO RECEIVE @ HQ (+1000)
        $poId = DB::table('purchase_orders')->insertGetId([
            'supplier_id'  => DB::table('suppliers')->where('code', 'SUP-ACME')->value('id'),
            'branch_id'    => $hq,
            'po_number'    => 'PO-2025-0001',
            'status'       => 'RECEIVED',
            'currency'     => 'USD',
            'total_amount' => 2200,
            'ordered_at'   => now()->subDays(10),
            'received_at'  => now()->subDays(9),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('purchase_order_items')->insert([
            'purchase_order_id' => $poId,
            'product_id'        => $prod,
            'unit_id'           => $unit,
            'qty_ordered'       => 1000,
            'qty_received'      => 1000,
            'unit_cost'         => 2.20,
            'line_total'        => 2200,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        self::postLedger($hq, $prod, $unit, 'PURCHASE_IN', +1000, $userSuper, 'PurchaseOrder', $poId);

        // 2) TRANSFER HQ -> PP (80)
        $trId = DB::table('transfers')->insertGetId([
            'from_branch_id' => $hq,
            'to_branch_id'   => $pp,
            'status'         => 'RECEIVED',
            'ref_no'         => 'TR-2025-0001',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('transfer_items')->insert([
            'transfer_id' => $trId,
            'product_id'  => $prod,
            'unit_id'     => $unit,
            'qty'         => 80,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        self::postLedger($hq, $prod, $unit, 'TRANSFER_OUT', -80, $userSuper, 'Transfer', $trId);
        self::postLedger($pp, $prod, $unit, 'TRANSFER_IN',  +80, $userAdmin, 'Transfer', $trId);

        // 3) TRANSFER PP -> DP (35)
        $tr2 = DB::table('transfers')->insertGetId([
            'from_branch_id' => $pp,
            'to_branch_id'   => $dp,
            'status'         => 'RECEIVED',
            'ref_no'         => 'TR-2025-0002',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('transfer_items')->insert([
            'transfer_id' => $tr2,
            'product_id'  => $prod,
            'unit_id'     => $unit,
            'qty'         => 35,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        self::postLedger($pp, $prod, $unit, 'TRANSFER_OUT', -35, $userAdmin, 'Transfer', $tr2);
        self::postLedger($dp, $prod, $unit, 'TRANSFER_IN',  +35, $userDist,  'Transfer', $tr2);

        // 4) SALE @ DP (20) after payment
        $so = DB::table('sales_orders')->insertGetId([
            'branch_id'           => $dp,
            'customer_name'       => 'Lucky Mart',
            'status'              => 'PAID',
            'requires_prepayment' => 1,
            'total_amount'        => 70.00,
            'currency'            => 'USD',
            'posted_at'           => now(),
            'posted_by'           => $userDist,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        DB::table('sales_order_items')->insert([
            'sales_order_id' => $so,
            'product_id'     => $prod,
            'unit_id'        => $unit,
            'qty'            => 20,
            'unit_price'     => 3.50,
            'line_total'     => 70.00,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('payments')->insert([
            'sales_order_id' => $so,
            'amount'         => 70.00,
            'currency'       => 'USD',
            'method'         => 'CASH',
            'paid_at'        => now(),
            'received_by'    => $userDist,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        self::postLedger($dp, $prod, $unit, 'SALE_OUT', -20, $userDist, 'SalesOrder', $so);

        // 5) ADJUST @ DP (-5 expired)
        $adj = DB::table('adjustments')->insertGetId([
            'branch_id'  => $dp,
            'reason'     => 'EXPIRE',
            'status'     => 'POSTED',
            'posted_at'  => now(),
            'approved_by' => $userAdmin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('adjustment_items')->insert([
            'adjustment_id' => $adj,
            'product_id'    => $prod,
            'unit_id'       => $unit,
            'qty_delta'     => -5,
            'note'          => 'Expired lot',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        self::postLedger($dp, $prod, $unit, 'ADJUST_OUT', -5, $userAdmin, 'Adjustment', $adj);
    }

    /**
     * Post a ledger row and update stock_levels snapshot (no negative checks here; demo only).
     */
    private static function postLedger($branch, $product, $unit, $type, $delta, $user, $refType, $refId): void
    {
        // 1) Read current on_hand for this Branch×Product×Unit
        $current = DB::table('stock_levels')
            ->where('branch_id',  $branch)
            ->where('product_id', $product)
            ->where('unit_id',    $unit)
            ->value('on_hand') ?? 0;

        // 2) New balance
        $after = $current + $delta;

        // 3) Insert immutable ledger row
        DB::table('inventory_ledger')->insert([
            'branch_id'      => $branch,
            'product_id'     => $product,
            'unit_id'        => $unit,
            'txn_type'       => $type,
            'qty_delta'      => $delta,
            'balance_after'  => $after,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'posted_at'      => now(),
            'posted_by'      => $user,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 4) Upsert the fast lookup row
        DB::table('stock_levels')->upsert([[
            'branch_id'  => $branch,
            'product_id' => $product,
            'unit_id'    => $unit,
            'on_hand'    => $after,
            'reserved'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['branch_id', 'product_id', 'unit_id'], ['on_hand', 'updated_at']);
    }
}
