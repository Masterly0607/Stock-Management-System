<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = ['adjustment_id', 'product_id', 'unit_id', 'qty_delta', 'note'];

    protected $casts = [
        'adjustment_id' => 'integer',
        'product_id'    => 'integer',
        'unit_id'       => 'integer',
        'qty_delta'     => 'decimal:2',
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class);
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
