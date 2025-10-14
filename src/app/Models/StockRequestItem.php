<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_request_id', 'product_id', 'unit_id', 'qty_requested', 'qty_approved'];

    protected $casts = [
        'stock_request_id' => 'integer',
        'product_id'       => 'integer',
        'unit_id'          => 'integer',
        'qty_requested'    => 'decimal:2',
        'qty_approved'     => 'decimal:2',
    ];

    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
