<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'barcode', 'category_id', 'unit_base_id', 'brand', 'is_active'];
    protected $casts = [
        'category_id' => 'integer',
        'unit_base_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_base_id');
    }
    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
