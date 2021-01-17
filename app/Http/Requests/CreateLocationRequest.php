<?php 

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLocationRequest extends FormRequest {

    public function rules() {
        return [
            'location_name' => 'required|string',
            'location_short_name' => 'required|string|unique:locations',
        ];
    }

}