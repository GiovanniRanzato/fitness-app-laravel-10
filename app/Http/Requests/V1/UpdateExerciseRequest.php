<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
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
            'name'        => ['string'],
            'description' => ['string', 'nullable'],
            'media_url'   => ['string', 'nullable'],
            'notes'       => ['string', 'nullable'],
            'creator_user_id' => ['exists:users,id'],
        ];
    }
}
