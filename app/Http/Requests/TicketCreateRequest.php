<?php 

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketCreateRequest extends FormRequest {

    public function rules() {
        return [
            'from_location_id' => 'required|integer',
            'to_location_id' => 'required|integer',
            'price' => 'required|integer',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
            'is_adapted' => 'required|boolean',
            'train_id' => 'required|integer'
        ];
    }

}