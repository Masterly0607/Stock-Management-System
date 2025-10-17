<?php

namespace App\Models\Concerns;

trait BelongsToBranch
{
  public static function bootBelongsToBranch(): void
  {
    static::creating(function ($m) {
      if (auth()->check() && empty($m->branch_id)) {
        $m->branch_id = auth()->user()->branch_id;
      }
    });
  }
  public function scopeForMyBranch($q)
  {
    $u = auth()->user();
    if (!$u || $u->hasRole('Super Admin')) return $q;
    return $q->where($q->getModel()->getTable() . '.branch_id', $u->branch_id);
  }
}
