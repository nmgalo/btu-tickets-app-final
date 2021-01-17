<?php 

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthenticateRequest extends FormRequest {

    public function rules() {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

}