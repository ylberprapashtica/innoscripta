<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return new ApiResponse('User has been logged in.');
        }else {
            return new ApiResponse('The provided credentials do not match our records.');
        }
    }

    public function register(Request $request): JsonResponse
    {
        $userData = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
        ]);

        $userEntity = User::create($userData);

        return new ApiResponse('New user is stored', $userEntity);
    }
}
