<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'name', 'code'];

    protected $casts = ['province_id' => 'integer'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
