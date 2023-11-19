<?php

namespace App\Providers;

use App\Models\CardDetail;
use App\Models\Card;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\User;


use App\Policies\CardDetailPolicy;
use App\Policies\CardPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ExercisePolicy;
use App\Policies\UserPolicy;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{


    /* The messages used on the methods
     */
    protected $messages = [
        'admin_only' => 'You must be an administrator.'
    ];

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CardDetail::class => CardDetailPolicy::class,
        Card::class => CardPolicy::class,
        Category::class => CategoryPolicy::class,
        Exercise::class => ExercisePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('user-view',            [UserPolicy::class, 'view']);
        Gate::define('user-view-any',        [UserPolicy::class, 'viewAny']);
        Gate::define('user-update',          [UserPolicy::class, 'update']);
        Gate::define('user-delete',          [UserPolicy::class, 'delete']);
        Gate::define('user-create',          [UserPolicy::class, 'create']);

        Gate::define('category-create',      [CategoryPolicy::class, 'create']);
        Gate::define('category-update',      [CategoryPolicy::class, 'update']);
        Gate::define('category-delete',      [CategoryPolicy::class, 'delete']);

        Gate::define('card-view',            [CardPolicy::class, 'view']);
        Gate::define('card-view-any',        [CardPolicy::class, 'viewAny']);
        Gate::define('card-create',          [CardPolicy::class, 'create']);
        Gate::define('card-update',          [CardPolicy::class, 'update']);
        Gate::define('card-delete',          [CardPolicy::class, 'delete']);

        Gate::define('card-detail-create',   [CardDetailPolicy::class, 'create']);
        Gate::define('card-detail-update',   [CardDetailPolicy::class, 'update']);
        Gate::define('card-detail-delete',   [CardDetailPolicy::class, 'delete']);

        Gate::define('exercise-view',        [ExercisePolicy::class, 'view']);
        Gate::define('exercise-view-any',    [ExercisePolicy::class, 'viewAny']);
        Gate::define('exercise-create',      [ExercisePolicy::class, 'create']);
        Gate::define('exercise-update',      [ExercisePolicy::class, 'update']);
        Gate::define('exercise-delete',      [ExercisePolicy::class, 'delete']);
    }
}

