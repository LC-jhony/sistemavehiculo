<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cargo;
use Illuminate\Auth\Access\HandlesAuthorization;

class CargoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cargo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cargo $cargo): bool
    {
        return $user->can('view_cargo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cargo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cargo $cargo): bool
    {
        return $user->can('update_cargo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cargo $cargo): bool
    {
        return $user->can('delete_cargo');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_cargo');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Cargo $cargo): bool
    {
        return $user->can('force_delete_cargo');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_cargo');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Cargo $cargo): bool
    {
        return $user->can('restore_cargo');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_cargo');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Cargo $cargo): bool
    {
        return $user->can('replicate_cargo');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_cargo');
    }
}
