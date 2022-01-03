<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function getAuthUser(Request $request) {
        try {
            // if(!Auth::user()) return Base::ERROR('User not logged in.');
            return Base::SUCCESS('Authenticated user fetched', Auth::user());
        } catch (Exception $e) {
            return Base::ERROR('Something went wrong', $e->getMessage());
        }
    }
    
    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());

            $email = strtolower($request->email);
            $user = User::where('email',$email)->first();
            if(!$user) return Base::ERROR("User not found");

            $login = $request->validate([
                'email' => 'required|string',
                'password' => 'required',
            ]);

            if (!Auth::attempt($login)) return Base::ERROR('Invalid Credentials', null);

            $accessToken = Auth::user()->createToken('authToken')->accessToken;

            $data = [
                'token' => $accessToken,
                'user' => [
                    "id" => Auth::user()->id,
                    "name" => Auth::user()->name,
                    "email" => Auth::user()->email,
                ],
            ];

            return Base::SUCCESS('Logged in successfully', $data);
        }
        catch (Exception $e)
        {
            return Base::ERROR('Login Failed', $e->getMessage());
        }
    }
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|confirmed',
            ]);
            if ($validator->fails()) return Base::SUCCESS($validator->errors()->first(), $validator->errors());

            $user_exist = User::where('email', strtolower($request->email))->first();
            if($user_exist) return Base::ERROR('User Already exist.');

            $user = new User();
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->save();

            // $data = [
            //     'name' => $user->name,
            //     'email' => $user->email
            // ];
            $accessToken = $user->createToken('authToken')->accessToken;

            $data = [
                'token' => $accessToken,
                'user' => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ],
            ];

            return Base::SUCCESS('User Successfully Registered.', $data);
        }
        catch (Exception $e) {
            return Base::ERROR('Registration failed', $e->getMessage());
        }
    }

    public function logout(Request $request) {
        try {
            // return Auth::user();
            // if(!Auth::user()) return Base::ERROR('User not logged in.');
            $user = Auth::user()->token();
            $user->revoke();
            return Base::SUCCESS('User successfully logged out');
        } catch (Exception $e) {
            return Base::ERROR('Logout failed', $e->getMessage());
        }
    }

}
