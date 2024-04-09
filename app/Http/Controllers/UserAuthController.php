<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserAuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request){
        try {
            $registerUserData = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|min:8'
            ]);
            $user = User::create([
                'first_name' => $registerUserData['first_name'],
                'last_name' => $registerUserData['last_name'],
                'email' => $registerUserData['email'],
                'password' => bcrypt($registerUserData['password']),
            ]);
            return response()->json([
                'message' => 'User Registred Successfully',
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Logs in a user
     */
    public function login(Request $request){
        try {
            $loginUserData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|min:8'
            ]);
            $user = User::where('email', $loginUserData['email'])->first();
            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            return response()->json([
                'message' => 'Logged in successfully',
                'access_token' => $token,
                'role' => $user->role,
                'company_id' => $user->company_id,
                'company_name' => $user->company->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logs out the user
     */
    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                "message" => "Logged out"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to logout",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
