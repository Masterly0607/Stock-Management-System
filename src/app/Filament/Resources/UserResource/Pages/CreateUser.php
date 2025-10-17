<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Branch;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // mutateFormDataBeforeCreate = Runs before the user record is saved to the database. You use it to add or change data that isn’t typed directly in the form. Ex: Before saving the new user, let’s calculate their branch.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $role  = $state['role'] ?? null;
        $prov  = $state['province_id'] ?? null;
        $dist  = $state['district_id'] ?? null;

        if ($role === 'Super Admin') {

            $data['branch_id'] = Branch::whereNull('province_id')->whereNull('district_id')->value('id');
        } elseif ($role === 'Admin') {
            $data['branch_id'] = Branch::where('province_id', $prov)->whereNull('district_id')->value('id')
                ?? Branch::whereNull('province_id')->whereNull('district_id')->value('id');
        } elseif ($role === 'Distributor') {

            $data['branch_id'] = Branch::where('province_id', $prov)->where('district_id', $dist)->value('id')
                ?? Branch::whereNull('province_id')->whereNull('district_id')->value('id'); // 
        }

        return $data;
    }

    // afterCreate() = Runs after the user has already been saved to the database. Ex: At this point, $this->record exists — it’s the new user row. Now you can safely run functions that need a saved user, like: $this->record->assignRole($role); because assignRole() needs an existing user_id. Now that the user is created, let’s assign their role.

    protected function afterCreate(): void
    {
        $role = $this->form->getRawState()['role'] ?? null;
        if ($role) {
            $this->record->assignRole($role);
        }
    }
}
