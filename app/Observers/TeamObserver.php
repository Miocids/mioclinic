<?php

namespace App\Observers;

use App\Enums\NotificationTemplateType;
use App\Models\NotificationTemplate;
use App\Models\Role;
use App\Models\Team;
use Spatie\Permission\PermissionRegistrar;

class TeamObserver
{
    public function created(Team $team): void
    {
        $this->createDefaultRoles($team);
        $this->createDefaultNotificationTemplates($team);
    }

    private function createDefaultRoles(Team $team): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        foreach (['super_admin', 'panel_user', 'partner', 'medico', 'paciente'] as $roleName) {
            Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
                'team_id'    => $team->id,
            ]);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }

    private function createDefaultNotificationTemplates(Team $team): void
    {
        foreach (NotificationTemplateType::cases() as $type) {
            NotificationTemplate::firstOrCreate(
                ['team_id' => $team->id, 'type' => $type->value],
                [
                    'subject'   => $type->defaultSubject(),
                    'body'      => $type->defaultBody(),
                    'is_active' => true,
                ],
            );
        }
    }
}
