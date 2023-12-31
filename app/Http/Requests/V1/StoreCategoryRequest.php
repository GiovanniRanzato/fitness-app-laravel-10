<?php

namespace App\Http\Requests\V1;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name'  => ['string', 'required'],
            'color' => ['string'],
            'icon'  => ['string'],
            'type'  => ['string'],
        ];
    }
}
