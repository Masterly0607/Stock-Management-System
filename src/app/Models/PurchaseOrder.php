<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'branch_id', 'po_number', 'status', 'currency', 'total_amount', 'ordered_at', 'received_at'];

    protected $casts = [
        'supplier_id'  => 'integer',
        'branch_id'    => 'integer',
        'total_amount' => 'decimal:2',
        'ordered_at'   => 'datetime',
        'received_at'  => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
