<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function reg(Request $request){
        $request -> validate(
            [
                'email' => 'required|email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'password' => ['required', Password::min(3)->letters()->numbers()->mixedCase()]
            ]);

        $user = User::create([
            'email' => $request -> email,
            'first_name' => $request -> first_name,
            'last_name' => $request -> last_name,
            'password' => Hash::make($request->password)
        ]);

       $token = $user-> createToken('Api token')->plainTextToken;

        return response() -> json([
            "success"=> true,
            "message"=> "Success",
            "token"=> $token

        ], 200);
    }

    public function authorization(Request $request) {
        $request -> validate([
           'email' => 'required|email',
           'password' => 'required'    
        ]);

        $user = User::where('email', $request-> email)->first();

        if($user && Hash::check($request-> password, $user-> password)){
            return response()->json([
                "success" => true,
                "message" => "Success",
                "token" => $user->createToken('api_token')->plainTextToken,
            ], 200);
        }

        return response() -> json([
            "success" => false,
            "message" => "Login failed"
         
        ], 401);
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
            "success" => true,
                "message" => "Logout"
            ],200);
    }
}
