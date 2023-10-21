<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
                'icon'      => $this->icon,
                'color'     => $this->color,
                'type'      => $this->type,
            ]
        ];
    }
}