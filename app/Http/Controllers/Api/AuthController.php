<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email'=>'required|email', 
            'password'=>'required|min:5', 
        ]);

        // dd($request->user());
        $credentials = $request->only(['email','password']);

        if(auth()->attempt($credentials)){
            /** @var App\Models\user*/
            $user = auth()->user();
            $token = $user->createToken('myToken')->plainTextToken;

            $user = [
                'name'=>$user->name,
                'email'=>$user->email,
                'access_token'=>$token,
            ];

            return response()->json(['message'=>'login success','user'=>$user],200);
        }

        return response()->json(['message'=>'email or password incorrect'],400);


    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message'=>'logout success'],200);

    }
    
}
