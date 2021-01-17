<?php 

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTrainRequest extends FormRequest {

    public function rules() {
        return [
            'model' => 'required|string',
            'train_seats_count_x' => 'required|integer',
            'train_seats_count_y' => 'required|integer',
            'available_class' => 'requred'
        ];
    }

}