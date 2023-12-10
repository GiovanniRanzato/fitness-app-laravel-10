<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CardDetail;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\CardDetailResource;
use App\Http\Requests\V1\StoreCardDetailRequest;
use App\Http\Requests\V1\UpdateCardDetailRequest;

class CardDetailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\V1\StoreCardDetailRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCardDetailRequest $request)
    {
        $access = Gate::inspect('card-detail-create');
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $data = $request->all();
        return new CardDetailResource(CardDetail::create($data)->load('exercise'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\V1\UpdateCardDetailRequest  $request
     * @param  \App\Models\CardDetails  $model
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCardDetailRequest $request, CardDetail $cardDetail)
    {
        $access = Gate::inspect('card-detail-update', $cardDetail);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);
        
        return new CardDetailResource($cardDetail->update($request->all()) ? $cardDetail->load('exercise') : []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\card_details  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(CardDetail $cardDetail)
    {
        $access = Gate::inspect('card-detail-delete', $cardDetail);
        if (!$access->allowed()) 
            return new Response(['message' => $access->message()], 401);

        $cardDetail->delete();
        return new Response(['message' => 'deleted'], 200);
    }
}
