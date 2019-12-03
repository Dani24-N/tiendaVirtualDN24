<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
            'nickname' => 'required|string',
            'type_document_id' => 'required|string',
            'number_document' => 'required|string',
            'city_id' => 'required|string',
            'role_id' => 'required|string',
            'state_id' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' =>'required|string|confirmed',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'lastname' => $request->lastname,
            'nickname' => $request->nickname,
            'type_document_id' => $request->type_document_id,
            'number_document' => $request->number_document,
            'city_id' => $request->city_id,
            'role_id' => $request->role_id,
            'state_id' => $request->state_id,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        return response()->json(['message' => 'Successfully created user!'],201);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'require|string|email',
            'password' => 'required|string',
            'remember_me' => 'bollean',
        ]);
        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials)){
            return response()->json(['message'=>'Unauthorized'],401);
        }

        $user =  $request->user();
        $tokenResult = $user->createToken('Personal Acces Token');
        $token = $tokenResult->token;

        if($request->remenber_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at)->toDateTimeString(),
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json(['message'=>'Successfully logged out']);
    }

    public function user (Request $request)
    {
        return response()->json($request->user());
    }
}
