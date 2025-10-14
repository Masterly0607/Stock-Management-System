<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['from_branch_id', 'to_branch_id', 'status', 'dispatched_at', 'received_at', 'ref_no'];

    protected $casts = [
        'from_branch_id' => 'integer',
        'to_branch_id'   => 'integer',
        'dispatched_at'  => 'datetime',
        'received_at'    => 'datetime',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
    public function items()
    {
        return $this->hasMany(TransferItem::class);
    }
}
