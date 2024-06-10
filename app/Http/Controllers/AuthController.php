<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{

    // register
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    // login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|unique:users, email',
            'password' => 'required|confirmed|string'
        ]);
        // check email
        $user = User::where('email', $fields['email'])->first();


        // check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {

            return response([
                'message' => 'Wrong creds'
            ], 401);
        }





        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    public function logout(Request $request)
    {
        auth()->user()->token()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
