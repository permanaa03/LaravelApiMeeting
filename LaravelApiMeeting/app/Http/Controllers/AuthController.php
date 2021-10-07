<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\posts;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if ($user->save()) {

            $token = null;
            try{
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json([
                        'msg' => 'Email or password are incorrect'
                    ],404);
                }
            }catch(JWTAuthException $e)
            {
                return response()->json([
                    'msg' => 'failed_to_create_token',
                ],404);
            }

            $user->signin = [
                'href' => 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email','password'
            ];
            $response = [
                    'msg' => 'User Created',
                    'user' => $user,
                    'token' => $token
            ];
            return response()->json($response, 201);
        }

            $response = [
                    'msg' => 'An error occured'
            ];
            return response()->json($response,404);
    }

    public function signin(Request $request)
    {
        echo "test function";   
    }

    public function login(Request $request)
    {
        $creds = $request->only(['email','password']);

        $token = auth()->attempt($creds);
        //options 2 
        // $token = JWTAuth::attempt($creds);
        
        if (!$token = JWTAuth::attempt($creds)) {    
            return response()->json([
                "status" => false,
                "messages" => "Unauthorized"
            ]);
        }
        return response()->json([
                "status" => true,
                "token" => $token,
                "user" => Auth::user()
        ]);
    }
        public function post_create(Request $request)
        {
            var_dump(Auth::user()->name);
            $post = new posts;
            $post->user_id = Auth::user()->id;
            $post->desc = $request->desc;

            if ($request->photo != '') {
                    $photo = time(). 'jpg';
                    file_put_contents('storage/posts/'.$photo,base64_decode($request->photo));
                    $post->photo = $photo;
            }

            $post->save();
            $post->user;
            return response()->json([
                    'success'=> true,
                    'message' => 'posted',
                    'post'=> $post
            ]);
        }
}
