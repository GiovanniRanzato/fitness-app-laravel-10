<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'quantity'       => ['integer'],
            'time_duration'  => ['integer'],
            'time_recovery'  => ['integer'],
            'weight'         => ['integer'],
            'notes'          => ['string'],
            'card_id'        => ['exists:cards,id'],
            'exercise_id'    => ['exists:exercises,id'],
        ];
    }
}
