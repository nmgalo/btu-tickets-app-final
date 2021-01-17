<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller {
    
    public function authenticate(AuthenticateRequest $request) {

        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials))
                return response()->json(['error' => 'invalid credentials'], 400);
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(RegisterRequest $request) {

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'role' => 'passenger',
            'password' => Hash::make($request->get('password')),
            'balance' => 0,
        ]);

        return response()->json(compact('user'), 201);
    }

    public function getAuthenticatedUser() {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate())
                return response()->json(['user_not_found'], 404);
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }



}
