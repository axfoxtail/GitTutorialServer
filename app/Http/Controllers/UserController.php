<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// Model
use App\User;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'name' => 'required|string|max:255',
    		'email' => 'required|string|max:255|unique:users',
    		'password' => 'required|string|max:255|min:6|confirmed',
    		'phone' => 'max:20|unique:users',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "messages" => $validator->messages()], 400);
    	}

        $user = User::forceCreate([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(80),
            'phone' => $request->phone ? $request->phone : "",
        ]);

        return response()->json(["status" => 200, "data" => $user, "message" => "New user has created successfully."], 200);
    }

    //
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'email' => 'required|string|max:255',
    		'password' => 'required|string|max:255|min:8',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "messages" => $validator->messages()], 400);
    	}
        
        $user = User::where('email', $request->email)->first();
        
        if (!($user && Hash::check($request->password, $user->password))) {
        	$messages = ["app" => ["User doesn't exist."]];
        	return response()->json(["status" => 400, "messages" => $messages], 400);
        }

        $user->is_loggedin = true;
        $user->last_login = date('c');
        $user->save();

        return response()->json(["status" => 200, "data" => $user, "message" => "User has logged in successfully."], 200);
    }

    //
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => 400, "messages" => $validator->messages()], 400);
        }
        
        $user = User::find($request->id);
        if(!$user) {
            return response()->json(["status" => 400, "messages" => $validator->messages()], 400);
        }
        $user->is_loggedin = false;
        $user->save();

        return response()->json(["status" => 200, "data" => $user, "message" => "User has logged out successfully."], 200);
    }
}
