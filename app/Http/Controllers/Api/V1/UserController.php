<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\UserFilter;
use App\Http\Controllers\Controller;

use App\Http\Requests\V1\DeleteUserRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserCollection;

use App\Models\User;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return App\Http\Resources\V1\UserCollection
     */
    public function index(Request $request)
    {
        $access = Gate::inspect('user-view-any');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $filter = new UserFilter();
        $filterItems = $filter->transform($request);
        $results = User::where($filterItems);
        $results = $results->with("category");
        return new UserCollection($results->paginate()->appends($request->query()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return App\Http\Resources\V1\UserResource
     */
    public function show(int $id)
    {
        $requestUser = User::find($id);

        if (!$requestUser)
            return new Response(['message' => 'Not Found.'], 404);
            
        $access = Gate::inspect('user-view', $requestUser);
        
        if (!$access->allowed()) {
            return new Response(['message' => $access->message()], 401);
        }
        return new UserResource($requestUser);
    }

    /**
     * Store a new resource in storage.
     *
     * @param  \App\Http\Requests\V1\StoreUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateUserRequest $request, User $user) {
        $access = Gate::inspect('user-create');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $data = $request->all();
        $data['password'] = bcrypt(uniqid());
        return new UserResource(User::create($data));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user) {
        $access = Gate::inspect('user-update', $user);
        if (!$access->allowed()) {
            return new Response(['message' => $access->message()], 401);
        }
        // if($request->media_url)
        //     $user->clearMediaCollection();
        //     $user->addMedia($request->media_url)
        //     ->preservingOriginal()
        //     ->toMediaCollection();

        $userData = $request->all();
        if(isset($userData['password'])) $userData['password'] = bcrypt($userData['password']);
        return new UserResource($user->update($userData) ? $user : []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\V1\DeleteUserRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        $access = Gate::inspect('user-delete', $user);
        if (!$access->allowed()) {
            return new Response(['message' => $access->message()], 401);
        }
        $user->delete();
        return new Response(['message' => 'deleted'], 200);

    }
}
