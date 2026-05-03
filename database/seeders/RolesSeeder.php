<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Team::each(function (Team $team): void {
            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

            foreach (['super_admin', 'panel_user', 'partner', 'medico', 'paciente'] as $roleName) {
                Role::firstOrCreate([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                    'team_id'    => $team->id,
                ]);
            }
        });

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }
}
