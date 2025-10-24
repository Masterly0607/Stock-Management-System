<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Branch;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Let Super Admin's selected branch_id pass through.
        // Only override for Admins (to lock to their branch)
        $me = auth()->user();

        if ($me?->hasRole('Admin')) {
            $data['branch_id'] = $me->branch_id;
            $data['role'] = 'Distributor';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $role = $this->form->getRawState()['role'] ?? null;

        if ($role) {
            $this->record->assignRole($role);
        }
    }
}
