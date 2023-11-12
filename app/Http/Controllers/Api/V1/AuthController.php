<?php

namespace App\Http\Controllers\Api\V1;


use Exception;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\V1\UserResource;
use App\Http\Requests\V1\UserLoginRequest;
use App\Http\Requests\V1\UserRegisterRequest;


class AuthController extends Controller
{
    /**
     * Register a new user on the app.
     *
     * @param  UserRegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register (UserRegisterRequest $request) {
        try {
            $userData = $request->all();
      

            $check_user_exist = User::where('email', $userData['email'])->first();
            if($check_user_exist)
                return response([
                    'message' => 'User already exist. Try different email.'
                ], 403);

            $userData['password'] = bcrypt($userData['password']);
            $user = User::create($userData);
            $token= $user->createAuthToken();

            $response = [
                'data' => new UserResource($user),
                'token' => $token
            ];
            return response($response, 201);
        } catch(Exception $e) {
            Log::error($e->getMessage());
            return response([
                'message' => 'An error occur. Try later...'
            ], 500);
        }
    }
    /**
     * Login the new user to the app.
     *
     * @param  UserLoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login (UserLoginRequest $request) {
        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)) {
            $user =  Auth::user();
            $token = $user->createAuthToken();
            $response = [
                'data' => new UserResource($user),
                'token' => $token
            ];
            return response($response, 200);
        }
        return response([
            'message' => 'Bad Credentials'
        ], 401);
    }
    /**
     * Logout the user from the app
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout (Request $request) {
        $request->user()->tokens()->delete();
       
        $response = [
            'message' => "Logged out"
        ];
        return response($response, 200);
    }

}
