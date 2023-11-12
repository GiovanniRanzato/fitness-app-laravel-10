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
        } else {
            return [
                'name'        => ['sometimes','string','required'],
                'email'       => ['sometimes','required','email:unique:users'],
                'password'    => ['sometimes','required','string','confirmed'],
                'category_id' => ['exists:categories,id'],
                'last_name'   => ['sometimes','string','nullable'],
                'phone'       => ['sometimes','string','nullable'],
                'birth_day'   => ['sometimes','string','nullable'],
                'sex'         => ['sometimes','string','nullable'],
                'weight'      => ['sometimes','string','nullable'],
                'height'      => ['sometimes','string','nullable'],
                'job'         => ['sometimes','string','nullable'],
                'country'     => ['sometimes','string','nullable'],
                'city'        => ['sometimes','string','nullable'],
                'postal_code' => ['sometimes','string','nullable'],
                'address'     => ['sometimes','string','nullable'],
                'role'        => ['sometimes','string','nullable'],
                'avatar'      => ['sometimes','string','nullable']

            ];  
        }
    }
}
