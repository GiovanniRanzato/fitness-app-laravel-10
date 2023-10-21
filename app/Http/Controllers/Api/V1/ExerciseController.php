<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\ExerciseResource;
use App\Http\Resources\V1\ExerciseCollection;
use App\Http\Requests\V1\StoreExerciseRequest;
use App\Http\Requests\V1\UpdateExerciseRequest;

class ExerciseController extends Controller
{

        /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $access = Gate::inspect('card-view-any');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $results = Exercise::permission($request->user());
        return new ExerciseCollection($results->paginate()->appends($request->query()));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\V1\StoreExerciseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExerciseRequest $request)
    {
        $access = Gate::inspect('exercise-create');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $data = $request->all();
        $data['creator_user_id'] = $request->user()->id;

        return new ExerciseResource(Exercise::create($data));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\UpdateExerciseRequest  $request
     * @param  \App\Models\Exercise  $model
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise)
    {
        $access = Gate::inspect('exercise-update', $exercise);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        return new ExerciseResource($exercise->update($request->all()) ? $exercise : []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exercise  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exercise $exercise)
    {
        $access = Gate::inspect('exercise-delete', $exercise);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
    }
}
