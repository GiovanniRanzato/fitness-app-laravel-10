<?php

namespace App\Http\Resources\V1;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
                'id'           => $this->id,
                'name'         => $this->name,
                'description'  => $this->description,
                'media_url'    => $this->media_url,
                'notes'        => $this->notes,
            ]
        ];
    }
}
