<?php

namespace App\Http\Resources\V1;

use DateTime;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'attributes' => [
                'id'        => $this->id,
                'name'      => $this->name,
                'disabled'  => $this->disabled,
                'date_from' => $this->formatDate($this->date_from),
                'date_to'   => $this->formatDate($this->date_to),
                'user_id'   => $this->user_id,
            ],
            'card_details' => CardDetailResource::collection($this->whenLoaded('cardDetails')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }

    private function formatDate ($date) {
        $date = New DateTime($date);
        return $date->format('Y-m-d');
    }
}
