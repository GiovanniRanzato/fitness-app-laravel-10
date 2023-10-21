<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $method = $this->method;
        if($method == 'PUT') {
            return [
                'name'        => ['required','string'],
                'email'       => ['required', 'email:unique:users', 'email'],
                'password'    => ['required','string','confirmed'],
                'category_id' => ['exists:categories,id'],
                'last_name'   => ['string'],
                'phone'       => ['string'],
                'birth_day'   => ['string'],
                'sex'         => ['string'],
                'weight'      => ['string'],
                'height'      => ['string'],
                'job'         => ['string'],
                'country'     => ['string'],
                'city'        => ['string'],
                'postal_code' => ['string'],
                'address'     => ['string'],
                'role'        => ['string'],
                'avatar'      => ['string']
            ];
        } else {
            return [
                'name'        => ['sometimes','string','required'],
                'email'       => ['sometimes','required','email:unique:users'],
                'password'    => ['sometimes','required','string','confirmed'],
                'category_id' => ['exists:categories,id'],
                'last_name'   => ['string'],
                'phone'       => ['string'],
                'birth_day'   => ['string'],
                'sex'         => ['string'],
                'weight'      => ['string'],
                'height'      => ['string'],
                'job'         => ['string'],
                'country'     => ['string'],
                'city'        => ['string'],
                'postal_code' => ['string'],
                'address'     => ['string'],
                'role'        => ['string'],
                'avatar'      => ['string']

            ];  
        }
    }
}
