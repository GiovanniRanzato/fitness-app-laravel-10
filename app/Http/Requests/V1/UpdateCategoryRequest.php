<?php

namespace App\Http\Requests\V1;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
                'name' => ['string', 'required'],
                'color' => ['string'],
                'icon' => ['string'],
                'type' => ['string'],
            ];
        } else {
            return [
                'name' => ['string','sometimes','required'],
                'color' => ['string'],
                'icon' => ['string'],
                'type' => ['string'],
            ];  
        }
    }
}
