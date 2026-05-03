<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private string $pendingRole = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingRole = $data['role'] ?? '';
        unset($data['role']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = parent::handleRecordCreation($data);

        $team = Filament::getTenant();
        $user->teams()->attach($team->id);

        if ($this->pendingRole) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
            $user->assignRole($this->pendingRole);
            app(PermissionRegistrar::class)->setPermissionsTeamId(null);
        }

        return $user;
    }
}
