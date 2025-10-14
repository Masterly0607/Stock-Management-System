<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'customer_name', 'status', 'requires_prepayment', 'total_amount', 'currency', 'posted_at', 'posted_by'];

    protected $casts = [
        'branch_id'          => 'integer',
        'requires_prepayment' => 'boolean',
        'total_amount'       => 'decimal:2',
        'posted_at'          => 'datetime',
        'posted_by'          => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
