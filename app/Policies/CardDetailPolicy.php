<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CardDetail;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardDetailPolicy
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
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\card_details  $cardDetails
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CardDetail $cardDetail)
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
     * @param  \App\Models\CardDetail  $cardDetail
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CardDetail $cardDetail)
    {
        if($user->isAdmin()) 
            return Response::allow();

        $cardDetail->load('card');
        if($user->isTrainer() && $user->id === $cardDetail->card->creator_user_id) 
            return Response::allow();

        return Response::deny($this->messages['not_allowed']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CardDetail  $cardDetail
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CardDetail $cardDetail)
    {
        if($user->isAdmin()) 
            return Response::allow();
    
        $cardDetail->load('card');
        if($user->isTrainer() && $user->id === $cardDetail->card->creator_user_id)
            return Response::allow();
    
        return Response::deny($this->messages['not_allowed']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CardDetail  $cardDetail
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CardDetail $cardDetail)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CardDetail  $cardDetail
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CardDetail $cardDetails)
    {
        return false;
    }
}
