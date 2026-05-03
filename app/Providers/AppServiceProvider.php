<?php

namespace App\Providers;

use App\Listeners\CreateUserProfile;
use App\Models\Appointment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Observers\AppointmentObserver;
use App\Observers\TeamObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Team::observe(TeamObserver::class);
        Appointment::observe(AppointmentObserver::class);

        Event::listen(RoleAttachedEvent::class, CreateUserProfile::class);

        Model::unguard();
    }
}
