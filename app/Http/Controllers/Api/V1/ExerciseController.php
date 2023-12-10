<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Filters\V1\ExerciseFilter;
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
        $access = Gate::inspect('exercise-view-any');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $filter = new ExerciseFilter();

        $filterItems = $filter->transform($request);
        $paginator = Exercise::permission($request->user())->where($filterItems)->paginate();
        return new ExerciseCollection($paginator->appends($request->query()));
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
     * Display the specified resource.
     *
     * @param  \App\Models\int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $requestExercise = Exercise::find($id);
        if (!$requestExercise)
            return new Response(['message' => 'Not Found.'], 404);

        $access = Gate::inspect('exercise-view', $requestExercise);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
  
        return new ExerciseResource($requestExercise); 
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
        $exercise->delete();
        return new Response(['message' => 'deleted'], 200);

    }
}
