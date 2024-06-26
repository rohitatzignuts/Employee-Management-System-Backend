<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
require_once app_path('Http/Helpers/APIResponse.php');

class UserAuthController extends Controller
{
    /**
     *  Register a new User
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @authentication Does not Require user authentication
     * @route /register
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $registerUserData = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|min:8|confirmed',
            ]);
            $user = User::create([
                'first_name' => $registerUserData['first_name'],
                'last_name' => $registerUserData['last_name'],
                'email' => $registerUserData['email'],
                'password' => bcrypt($registerUserData['password']),
                'created_by' => auth()->user()->id,
            ]);
            return ok('User Registred Successfully', $user);
        } catch (\Exception $e) {
            return error('Failed to register the user : ' . $e->getMessage());
        }
    }

    /**
     *  Log in the registered user
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @authentication Does not Require user authentication
     * @route /login
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $loginUserData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|min:8',
            ]);
            $user = User::where('email', $loginUserData['email'])->first();
            // check if user exists or not
            if (!$user) {
                return error('User Not Found!!');
            }
            // Check Credentials
            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return error('Invalid Login Details !!');
            }
            // Create Token
            $token = $user->createToken('LoginToken')->plainTextToken;
            return response()->json([
                'message' => 'Logged in successfully',
                'access_token' => $token,
                'role' => $user->role,
                'company_id' => $user->company_id ?? null,
                'company_name' => $user->company ? $user->company->name : null,
            ]);
        } catch (\Exception $e) {
            return error('An error occurred', $e->getMessage());
        }
    }

    /**
     * Reset already existing users password
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /resetPassword
     * @authentication Requires user authentication
     * @middleware auth:sanctum
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        try {
            $loginUserData = $request->validate([
                'email' => 'required|string|email',
                'oldPassword' => 'required|min:8',
                'newPassword' => 'required|min:8',
            ]);
            $user = User::where('email', $loginUserData['email'])->first();
            // check for the old password
            if (!$user || !Hash::check($loginUserData['oldPassword'], $user->password)) {
                return error('Invalid Credentials');
            }
            $user->update([
                'password' => bcrypt($loginUserData['newPassword']),
                'updated_by' => auth()->user()->id,
            ]);
            return ok('Password reset successfully');
        } catch (\Exception $e) {
            return error('An error occurred', $e->getMessage());
        }
    }

    /**
     * Logout existing user
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /logout
     * @authentication Requires user authentication
     * @middleware auth:sanctum
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        try {
            // delete user's authentication token
            auth()->user()->tokens()->delete();
            return ok('Logged out SuccessFully!');
        } catch (\Exception $e) {
            return error('An error occurred', $e->getMessage());
        }
    }
}
