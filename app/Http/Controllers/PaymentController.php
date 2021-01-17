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

	    DB::beginTransaction();

	    try {

	    	$user = User::where("email", $request->get("user_email"))->get()->first();
		    $existingBalance = $user->balance ?: 0;
		    $userId = $user->id;

		    $update = User::find($userId)->update(["balance" => $existingBalance + $request->get("amount")]);

	    	DB::commit();
	    	return response()->json(["result" => "success"], 201);
		} catch (\Exception $e) {
		    DB::rollback();
		    return response()->json(["result" => "failed"], 500);
		}


    }
}
