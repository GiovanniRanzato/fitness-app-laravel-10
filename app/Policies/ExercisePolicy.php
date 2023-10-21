<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExercisePolicy
{
    use HandlesAuthorization;
    /* The messages used on the methods */
    protected $messages = [
        'not_allowed' => 'Access denied: you are not allowed.'
    ];

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Exercise $exercise)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\exercises  $exercises
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if($user->isAdmin()) 
            return Response::allow();

        if($user->isTrainer()) 
            return Response::allow();

        return Response::deny($this->messages['not_allowed']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exercise  $exercises
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Exercise $exercise)
    {
        if($user->isAdmin()) 
            return Response::allow();

        if($user->isTrainer() && $user->id === $exercise->creator_user_id) 
            return Response::allow();

        return Response::deny($this->messages['not_allowed']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exercise  $exercises
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Exercise $exercise)
    {
        if($user->isAdmin()) 
            return Response::allow();

        if($user->isTrainer() && $exercise->creator_user_id == $user->id){
            return Response::allow();
        }

        return Response::deny($this->messages['not_allowed']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exercise  $exercises
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Exercise $exercise)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\exercises  $exercises
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, exercises $exercises)
    {
        return false;
    }
}
