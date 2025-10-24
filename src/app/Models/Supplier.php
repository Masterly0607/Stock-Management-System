<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use App\Models\Concerns\HasBranchScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, HasBranchScopes, BelongsToBranch;

    protected $fillable = ['name', 'phone', 'address', 'branch_id', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'branch_id' => 'integer',];
}
