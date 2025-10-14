<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Unit;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $supp = Supplier::firstOrCreate(['code' => 'SUP-ABC'], [
            'name' => 'ABC Manufacturing Co.',
            'phone' => '+855 12 345 678',
            'email' => 'sales@abc-mfg.com',
            'contact_name' => 'Mr. Chan',
            'address' => 'Phnom Penh',
            'is_active' => true,
        ]);

        $hqId = Branch::where('code', 'HQ')->value('id');

        $po = PurchaseOrder::firstOrCreate(['po_number' => 'PO-' . Str::upper(Str::random(6))], [
            'supplier_id' => $supp->id,
            'branch_id' => $hqId,
            'status' => 'ORDERED',
            'currency' => 'USD',
            'total_amount' => 0,
            'ordered_at' => now(),
        ]);

        $unitId = Unit::where('name', 'Piece')->value('id');
        $p1 = Product::where('sku', 'COC-1L')->first();
        $p2 = Product::where('sku', 'SHAM-500')->first();

        $lines = [
            ['product' => $p1, 'qty' => 200, 'cost' => 1.10],
            ['product' => $p2, 'qty' => 150, 'cost' => 2.40],
        ];

        $total = 0;
        foreach ($lines as $ln) {
            if (!$ln['product']) continue;
            $lineTotal = $ln['qty'] * $ln['cost'];
            $total += $lineTotal;
            PurchaseOrderItem::firstOrCreate(
                ['purchase_order_id' => $po->id, 'product_id' => $ln['product']->id, 'unit_id' => $unitId],
                ['qty_ordered' => $ln['qty'], 'qty_received' => 0, 'unit_cost' => $ln['cost'], 'line_total' => $lineTotal]
            );
        }

        $po->update(['total_amount' => $total]);
    }
}
