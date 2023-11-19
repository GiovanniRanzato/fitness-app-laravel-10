<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name'        => ['required','string'],
            'email'       => ['required', 'email:unique:users', 'email'],
            'password'    => ['required','string','confirmed'],
            'category_id' => ['exists:categories,id'],
            'last_name'   => ['string','nullable'],
            'phone'       => ['string','nullable'],
            'birth_day'   => ['string','nullable'],
            'sex'         => ['string','nullable'],
            'weight'      => ['string','nullable'],
            'height'      => ['string','nullable'],
            'job'         => ['string','nullable'],
            'country'     => ['string','nullable'],
            'city'        => ['string','nullable'],
            'postal_code' => ['string','nullable'],
            'address'     => ['string','nullable'],
            'role'        => ['string','nullable'],
            'avatar'      => ['string','nullable']
        ];
    }
}