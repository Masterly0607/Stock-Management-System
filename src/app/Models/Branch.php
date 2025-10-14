<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'province_id', 'district_id'];

    protected $casts = [
        'province_id' => 'integer',
        'district_id' => 'integer',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }
}
