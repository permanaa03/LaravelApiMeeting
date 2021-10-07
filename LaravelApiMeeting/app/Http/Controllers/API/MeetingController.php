<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Meeting;
use JWTAuth;   
use Illuminate\Support\Facades\Auth;
use App\posts; 
use App\User;

class MeetingController extends Controller
{

    public function __construct()
    {

        //authentiacation JWTAuth before access MEeeting
        // $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $meetings = Meeting::orderBy('id','desc')->get();
        foreach ($meetings as $meeting) {
            $meeting->view_meeting = [
                    'href' => 'api/v1/meeting/' . $meeting->id,
                    'method' => 'GET'
            ];
            $meeting->users;
            
        }

        $response = [
            'msg' => 'List of all meetings',
            'meeting' => $meetings
        ];
        return response()->json($response,200);
   
    }

    public function showdata()
    {
        //view on route web;
        return view('Meeting.showdata');
    }

    /**
     * Store a newly created resource in storage.v
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required',
        ]);
        
        $title = $request->input('title');
        $description = $request->input('description');
        $user_id = $request->input('user_id');
        $time = $request->input('time');

        $meeting = new Meeting([
            'time' => $time,
            'title' => $title,
            'description'=> $description,
            'user_id' => $user_id
        ]);

        
        if ($meeting->save()) {
            // $meeting->users()->attach($user_id); //fungsi attach adalah untuk membuat table join dengan foreign key update 
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/' . $meeting->id,
                'method'=> 'GET'
            ];
        }

        $message = [
            'msg' => 'Meeting Created',
            'data' => $meeting

        ];
        return response()->json($message,201);
        
        $response = [
            'msg' => 'Error during creation'
        ];

        return response()->json($response,404);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id',$id)->firstOrFail();
        $meeting->view_meetings = [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];

        $response = [
            'msg' =>'Meeting information',
            'meeting' => $meeting
        ];

        return response()->json($response,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required',
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $user_id = $request->input('user_id');
        $time = $request->input('time');

        $meeting = Meeting::with('users')->findOrFail($id);

        if (!$meeting->users()->where('users.id',$user_id)->first()) {
            return response()->json(['msg' => 'user not registered for meeting, update not successfully'],401);
        }

        $meeting->time = $time;
        $meeting->title = $title;
        $meeting->description = $description;

        if (!$meeting->update()) {
            return response()->json([
                    'msg' => 'Error during update'],404);
        }

        $meeting->view_meeting = [
            'href' => 'api/v1/meeting/' . $meeting->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Meeting Updated',
            'meeting' => $meeting
        ];

        return response()->json($response,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users;
        $meeting->users()->detach();

        if (!$meeting->delete()) {
            foreach ($users as $user) {
                    $meeting->users()->attach($user);
            }
            return response()->json([
                'msg' =>'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Meeting Deleted',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title','description','time'
            ]
        ];
        return response()->json($response,200);
    }


    public function logout(Request $request)
    {
        try{
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return response()->json([
                'success' => true,
                'message' => 'logout success'
            ]);

        }catch(Excepetion $e){
            return response()->json([
                'success' => false,
                'message' => 'logout failed'. $e
            ]);
        }
    }

    public function posts()
    {
        $posts = posts::orderBy('id','desc')->get();
        foreach ($posts as $post) {
            $post->user;
        }

        return response()->json([
            'success' => true,
            'message'=> 'success get data',
            'posts' => $posts
        ]);
    }


    public function testing_jwt_regist(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|max:10|min:6'
        ]);

        $creds = $request->only('email','password');

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        $token=false;
        if ($user->save()) {
   
        if (!$token = JWTAuth::attempt($creds)) {
            return response()->json([
                'status' => false,
                'message'=> "user failed created"
            ]);
        }

        return response()->json([
            'status' => true,
            'messages'=> "data user has been created",
            'user' => $user,
            'token' => $token
        ]);
    }

    }
}
