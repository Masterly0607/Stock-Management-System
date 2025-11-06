<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adjustment extends Model
{
    public function items()
    {
        return $this->hasMany(AdjustmentItem::class);
    }
    // If you kept 'lines' in service:
    public function lines()
    {
        return $this->items();
    }
}
