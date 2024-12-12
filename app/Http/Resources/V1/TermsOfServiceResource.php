<?php

namespace App\Http\Resources\V1;

use DateTime;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TermsOfServiceResource extends JsonResource
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
                'id'         => $this->id,
                'text'       => $this->text,
                'updated_at' => $this->formatDate($this->updated_at),
            ]
        ];
    }

    private function formatDate ($date) {
        $date = New DateTime($date);
        return $date->format('Y-m-d');
    }
}