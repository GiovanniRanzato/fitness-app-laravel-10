<?php

namespace App\Http\Resources\V1;

use DateTime;
use App\Models\Category;
use App\Http\Resources\V1\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $model = $this->resource;
        $model->loadMissing(['category', 'cards']);
        // $mediaItems = $model->getMedia();
        // $media_url = isset($mediaItems[0]) ? $mediaItems[0]->getUrl() : '';
       
        // $included = [];
        // if($model->relationships && $model->relationships['categories'])
        //     $included['category'] = 

        return [
            'attributes' => [
                'id'          => $this->id,
                'category_id' => $this->category_id,
                'name'        => $this->name,
                'email'       => $this->email,
                'role'        => $this->role,
                'last_name'   => $this->last_name,
                'phone'       => $this->phone,
                'birth_day'   => $this->formatDate($this->birth_day),
                'sex'         => $this->sex,
                'weight'      => $this->weight,
                'height'      => $this->height,
                'job'         => $this->job,
                'country'     => $this->country,
                'city'        => $this->city,
                'postal_code' => $this->postal_code,
                'address'     => $this->address,
                'role'        => $this->role,
                'avatar'      => $this->avatar,

            ],
            'included' => [
                'category' => new CategoryResource($this->category)
                // cards
                // 'media_url' => $media_url
            ]
           
        ];
    }

    private function formatDate ($date) {
        $date = New DateTime($date);
        return $date->format('Y-m-d');
    }
}