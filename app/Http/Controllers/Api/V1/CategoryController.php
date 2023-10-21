<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Filters\V1\CategoryFilter;
use App\Http\Requests\V1\DeleteCategoryRequest;
use App\Http\Requests\V1\StoreCategoryRequest;
use App\Http\Requests\V1\UpdateCategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\CategoryCollection;
use App\Models\Category;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
class CategoryController extends Controller
{    
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = new CategoryFilter();
        $filterItems = $filter->transform($request);
        $results = Category::where($filterItems);
        return new CategoryCollection($results->paginate()->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\V1\StoreCategoryRequest; $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $access = Gate::inspect('category-create');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        return new CategoryResource(Category::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $requestCategory = Category::find($id);

        if (!$requestCategory)
            return new Response(['message' => 'Not Found.'], 404);
                    
        return new CategoryResource($requestCategory); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $access = Gate::inspect('category-update');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
        
        return new CategoryResource($category->update($request->all()) ? $category : []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\V1\DeleteCategoryRequest $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $access = Gate::inspect('category-delete');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
        $category->delete();
        return new Response(['message' => 'deleted'], 200);

    }
}
