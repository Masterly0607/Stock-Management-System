<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'symbol', 'base_ratio'];
    protected $casts = ['base_ratio' => 'integer'];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
