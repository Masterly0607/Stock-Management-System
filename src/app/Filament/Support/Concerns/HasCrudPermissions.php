<?php

namespace App\Filament\Support\Concerns;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

// UI visibility & actions based on user roles
trait HasCrudPermissions
{
  /** Can the user see the resource list at all? */
  public static function canViewAny(): bool
  {
    $user = Filament::auth()->user();
    return $user?->hasAnyRole(['Super Admin', 'Admin']) ?? false;
  }

  /** Can the user create new records? */
  public static function canCreate(): bool
  {
    $user = Filament::auth()->user();
    return $user?->hasRole('Super Admin') ?? false;
  }

  /** Can the user delete this record? */
  public static function canDelete(Model $record): bool
  {
    $user = Filament::auth()->user();
    return $user?->hasRole('Super Admin') ?? false;
  }
}
