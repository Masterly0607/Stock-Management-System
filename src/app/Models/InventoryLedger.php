<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryLedger extends Model
{
    use HasFactory;

    protected $table = 'inventory_ledger';

    protected $fillable = ['txn_type', 'txn_id', 'branch_id', 'product_id', 'unit_id', 'qty_delta', 'reference', 'notes', 'posted_at', 'posted_by'];

    protected $casts = [
        'txn_id'    => 'integer',
        'branch_id' => 'integer',
        'product_id' => 'integer',
        'unit_id'   => 'integer',
        'qty_delta' => 'decimal:2',
        'posted_at' => 'datetime',
        'posted_by' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
