<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;
    public function register (RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email'=> $data['email'],
            'password'=> bcrypt($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->toResource(),
            'token' => $token,
        ], 'Registration successfully!', statusCode: 201);
    }

    public function login(LoginRequest $request) {
        $data = $request->validated();

        if (!Auth::guard('api')->attempt($data)) {
            return $this->errorResponse('Invalid credentials!', statusCode: 401);
        }

        $user = Auth::guard('api')->user();

        if (!$user->is_active) {
            return $this->errorResponse('This account is disabled!', statusCode: 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user'=> $user->toResource(),
            'token'=> $token,
        ], 'Login successfully!');
    }

    public function me(Request $request) {
        return $this->successResponse($request->user(), 'User data successfully retrieved');
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully!');
    }
}
