<?php

namespace App\Http\Resources\V1;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class CardDetailResource extends JsonResource
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
                'quantity'      => $this->quantity,
                'time_duration'  => $this->time_duration,
                'time_recovery' => $this->time_recovery,
                'weight'   => $this->weight,
                'notes'   => $this->notes,
            ],
            'exercise' => new ExerciseResource($this->whenLoaded('exercise')),          
        ];
    }
}