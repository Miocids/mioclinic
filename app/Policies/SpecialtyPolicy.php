<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Specialty;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecialtyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Specialty');
    }

    public function view(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('View:Specialty');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Specialty');
    }

    public function update(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('Update:Specialty');
    }

    public function delete(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('Delete:Specialty');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Specialty');
    }

    public function restore(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('Restore:Specialty');
    }

    public function forceDelete(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('ForceDelete:Specialty');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Specialty');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Specialty');
    }

    public function replicate(AuthUser $authUser, Specialty $specialty): bool
    {
        return $authUser->can('Replicate:Specialty');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Specialty');
    }

}