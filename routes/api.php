<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketsController;

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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);

Route::group([
	'middleware' => ['jwt.verify'],
	'prefix' => "admin"
], function() {
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::get('closed', [UserController::class, 'closed']);
});


Route::group(['prefix' => 'v1/tickets'], function() {
	Route::get("stations/timetable", [TicketsController::class, 'getTicketsTimeTable']);
	Route::get("order/{ticketId}", [TicketsController::class, 'getTicketDetails']);
});
