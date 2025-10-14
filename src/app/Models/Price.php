<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'province_id', 'unit_id', 'price', 'currency', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'product_id'  => 'integer',
        'province_id' => 'integer',
        'unit_id'     => 'integer',
        'price'       => 'decimal:2',
        'is_active'   => 'boolean',
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
