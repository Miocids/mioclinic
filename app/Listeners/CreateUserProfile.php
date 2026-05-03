<?php

namespace App\Listeners;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Role;
use Spatie\Permission\Events\RoleAttachedEvent;

class CreateUserProfile
{
    public function handle(RoleAttachedEvent $event): void
    {
        $user = $event->model;
        $roleIds = (array) $event->rolesOrIds;

        $roles = Role::whereIn('id', $roleIds)->get();

        foreach ($roles as $role) {
            match ($role->name) {
                'paciente' => Patient::firstOrCreate([
                    'team_id' => $role->team_id,
                    'user_id' => $user->id,
                ]),
                'medico' => Doctor::firstOrCreate([
                    'team_id' => $role->team_id,
                    'user_id' => $user->id,
                ]),
                default => null,
            };
        }
    }
}
