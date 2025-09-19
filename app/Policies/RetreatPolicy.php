<?php

namespace App\Policies;

use App\Models\Retreat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RetreatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_retreat');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Retreat $retreat): bool
    {
        return $user->can('view_retreat') || 
               ($retreat->is_active && $user->can('view_public_retreat'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_retreat');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Retreat $retreat): bool
    {
        return $user->can('update_retreat') || 
               ($user->id === $retreat->created_by && $user->can('update_own_retreat'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Retreat $retreat): bool
    {
        return $user->can('delete_retreat') || 
               ($user->id === $retreat->created_by && $user->can('delete_own_retreat'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Retreat $retreat): bool
    {
        return $user->can('restore_retreat');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Retreat $retreat): bool
    {
        return $user->can('force_delete_retreat');
    }

    /**
     * Determine whether the user can book the retreat.
     */
    public function book(User $user, Retreat $retreat): bool
    {
        // Only active retreats can be booked
        if (!$retreat->is_active) {
            return false;
        }

        // Check if retreat has available seats
        if ($retreat->seats <= $retreat->bookings()->count()) {
            return false;
        }

        // Check if retreat has started
        if ($retreat->start_date->isPast()) {
            return false;
        }

        // Check if user has already booked this retreat
        if ($retreat->bookings()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return $user->can('book_retreat');
    }
}
