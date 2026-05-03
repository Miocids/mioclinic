<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private string $pendingRole = '';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $team = Filament::getTenant();

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $data['role'] = $this->record->roles->first()?->name ?? '';
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingRole = $data['role'] ?? '';
        unset($data['role']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->pendingRole) {
            $team = Filament::getTenant();

            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
            $this->record->syncRoles([$this->pendingRole]);
            app(PermissionRegistrar::class)->setPermissionsTeamId(null);
        }
    }
}
