<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserAuthController extends Controller
{
    public function register(Request $request){
        $registerUserData = $request->validate([
            'first_name'=>'required|string',
            'last_name'=>'required|string',
            'role' => ['required', Rule::in(['admin', 'employee', 'cmp_employee', 'candidate'])],
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);
        $user = User::create([
            'first_name' => $registerUserData['first_name'],
            'last_name' => $registerUserData['last_name'],
            'email' => $registerUserData['email'],
            'role' => $registerUserData['role'],
            'password' => bcrypt($registerUserData['password']),
        ]);
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return response()->json([
            'message' => 'User Created ',
            'access_token' => $token,
        ]);
    }

    public function login(Request $request){
        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);
        $user = User::where('email',$loginUserData['email'])->first();
        if(!$user || !Hash::check($loginUserData['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token,
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            "message"=>"logged out"
        ]);
    }
}
