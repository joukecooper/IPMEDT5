<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppUser;

class AppUserController extends Controller
{
    
    public function register_user(Request $request){

        $username = $request->input('username');
        $password = $request->input('password');

        $exists = AppUser::where('username', $username)->exists();

        if ($exists) {

            return response()->json(['error' => 'User already exists'], 400);

        } else {
            
            $new_user = new AppUser();

            $new_user->username = $username;
            $new_user->password = $password;

            $new_user->save();
            return response()->json([], 201);

        }

    }

    public function login_user(Request $request){

        $username = $request->input('username');
        $password = $request->input('password');
    
        $exists = AppUser::where('username', $username)->exists();
    
        if (!$exists) {
            return response()->json(['error' => 'User does not exist'], 400);
        } else {
            $match = AppUser::where('username', $username)
                            ->where('password', $password)
                            ->exists();
            
            if (!$match){
                return response()->json(['error' => 'Incorrect password'], 400);
            } else {
                // Authentication successful
                return response()->json([], 201);
            }
        }
    }

}
