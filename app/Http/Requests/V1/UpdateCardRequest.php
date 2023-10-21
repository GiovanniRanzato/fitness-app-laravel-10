<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
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
                'name'      => ['string','required'],
                'disabled'  => ['boolean'],
                'date_from' => ['string'],
                'date_to'   => ['string'],
                'user_id'   => ['exists:users,id'],
            ];
        } else {
            return [
                'name'      => ['string','sometimes','required'],
                'disabled'  => ['boolean'],
                'date_from' => ['string'],
                'date_to'   => ['string'],
                'user_id'   => ['exists:users,id'],
            ];  
        }
    }
}
