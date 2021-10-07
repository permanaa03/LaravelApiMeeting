<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    public function authdata(Request $request)
    {
        $creds = $request->only(['email','password']);

        if (!$token=auth()->attempt($creds)) {
                return response()->json([
                    'success' => false
                ]);
            }
                return response()->json([
                    'succces' => true,
                    'token' => $token,
                    'user'=> Auth::user()
                ]);
    }

    public function delete_user(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $user->user_meeting()->detach();
        

        if (!$user->delete()) {
            return response()->json([
                'status' => false,
                'message' => 'data user failed deleted'
            ]);
          
        }
        return response()->json([
            'status' => true,
            'message' => 'data has been deleted',
            'users' => $user
        ]);
    }
}
