<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
	'prefix' => 'balance'
], function() {
    Route::post('top-up', [PaymentController::class, 'topUpUserBalance']);
});


Route::group([
	'middleware' => ['jwt.verify.admin'],
	'prefix' => "admin"
], function() {

    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::post('ticket/create', [TicketsController::class, 'createNewTicket']);

    Route::post('location/create', [TicketsController::class, 'createNewLocation']);

    Route::post('train/create', [TicketsController::class, 'createNewTrain']);

});


Route::group([
	'prefix' => 'passenger'
], function() {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'authenticate']);
});


Route::group(['prefix' => 'v1/tickets'], function() {
	Route::get("stations/timetable", [TicketsController::class, 'getTicketsTimeTable']);
	Route::get("order/{ticketId}", [TicketsController::class, 'getTicketDetails']);
	Route::get("ordet/seat/{locationHash}", [TicketsController::class, 'chooseSeat']);
});
