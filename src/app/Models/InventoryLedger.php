<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLedger extends Model
{
    protected $table = 'inventory_ledger';

    protected $fillable = [
        'product_id',
        'branch_id',
        'movement',
        'qty',
        'balance_after',
        'source_type',
        'source_id',
        'source_line',
        'posted_at',
        'posted_by',
        'hash'
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'qty' => 'float',
        'balance_after' => 'float',
    ];
}
