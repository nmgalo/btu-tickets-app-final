<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class PaymentController extends Controller
{
    // TODO implement functions
    public function topUpUserBalance(Request $request) {

		$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
	        'user_email' => 'required|email',
	        'amount' => 'required|integer' 
	    ]);

	    if($validator->fails())
	        return response()->json($validator->errors(), 400);

	    $user = User::where("email", $request->get("user_email"))->update(['amount' => 1]);

	    dd($usere);

	    if ($userUpdate)
	    	return response()->json(["result" => "success"], 201);

	    return response()->json(["result" => "failed"], 500);

    }
}
