<?php

namespace App\Http\Controllers\api;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function login(Request $request)
    {
        try{
            $request->validate([
                'email'     => 'email|required',
                'password'  => 'required'
            ]);

            $credentials = request(['email', 'password']); 
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }
            // cek email & password
            $user = User::where('email', $request->email)->first();
            // jika email salah
            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }
            // jika benar
            // buat token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token'  => $tokenResult,
                'token_type'    => 'Bearer',
                'user'          => $user
            ], 'Authenticated');
        } catch(Exception $error){
            return ResponseFormatter::error([
                'message'   => 'Something went wrong',
                'error'     => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {
        try{

            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|string|email|unique:users|max:255',
                'password'  => $this->passwordRules()
            ]);

            User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'address'       => $request->address,
                'house_number'  => $request->house_number,
                'phone_number'  => $request->phone_number,
                'city'          => $request->city,
                'password'      => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token'  => $tokenResult,
                'token_type'    => 'Bearer',
                'user'          => $user
            ], 'Authenticated');

        }catch(Exception $error){
            return ResponseFormatter::error([
                'message'   => 'Something went wrong',
                'error'     => $error
            ], 'Authentication Failed', 500);
        }
    }

}
