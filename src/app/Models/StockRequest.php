<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = ['requested_by_user_id', 'request_branch_id', 'source_branch_id', 'status', 'requested_at', 'approved_at', 'approved_by'];

    protected $casts = [
        'requested_by_user_id' => 'integer',
        'request_branch_id'    => 'integer',
        'source_branch_id'     => 'integer',
        'approved_by'          => 'integer',
        'requested_at'         => 'datetime',
        'approved_at'          => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
    public function requestBranch()
    {
        return $this->belongsTo(Branch::class, 'request_branch_id');
    }
    public function sourceBranch()
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }
    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }
}
