<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    public function items()
    {
        return $this->hasMany(TransferItem::class);
    }
    public function lines()
    {
        return $this->items();
    }
}
