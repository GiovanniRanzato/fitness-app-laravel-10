<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Filters\V1\CardFilter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\CardResource;
use App\Http\Resources\V1\CardCollection;
use App\Http\Requests\V1\StoreCardRequest;
use App\Http\Requests\V1\UpdateCardRequest;

class CardController extends Controller
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

        $filter = new CardFilter();

        $filterItems = $filter->transform($request);
        $paginator = Card::permission($request->user())->where($filterItems)->paginate();
        $results = $paginator->getCollection()->loadMissing('user', 'cardDetails.exercise');
        $paginator->setCollection($results);

        return new CardCollection($paginator->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\V1\StoreCardRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCardRequest $request)
    {
        $access = Gate::inspect('card-create');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $data = $request->all();
        $data['creator_user_id'] = $request->user()->id;

        $card = Card::create($data);
        $card->loadMissing('user', 'cardDetails.exercise');
        return new CardResource($card);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $requestCard = Card::with('cardDetails.exercise')->find($id);
        if (!$requestCard)
            return new Response(['message' => 'Not Found.'], 404);

        $access = Gate::inspect('card-view', $requestCard);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
  
        $requestCard->loadMissing('user', 'cardDetails.exercise');
        return new CardResource($requestCard); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\UpdateCardRequest  $request
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCardRequest $request, Card $card)
    {
        $access = Gate::inspect('card-update', $card);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        
        $card->update($request->all());
        $card->loadMissing('user', 'cardDetails.exercise');

        return new CardResource($card);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        $access = Gate::inspect('card-delete', $card);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $card->delete();
        return new Response(['message' => 'deleted'], 200);
    }
}
