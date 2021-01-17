<?php 

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest {

    public function rules() {
        return [
            'user_email' => 'required|email',
	        'amount' => 'required|integer' 
        ];
    }

}