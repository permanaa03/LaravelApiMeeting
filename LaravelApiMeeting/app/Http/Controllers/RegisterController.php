<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use App\User;
class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
     
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User is already registered for meeting',
            'user' => $user,
            'meeting' => $meeting,
            'unregister' => [
                'href' => 'api/v1/registration' . $meeting->id,
                'method' => 'DELETE',
            ]
        ];
            if ($meeting->users()->where('users.id',$user->id)->first()) {
                return response()->json($message,404);
            }

            $user->meetings()->attach($meeting);

            $response = [
                'msg' => 'User registered for meeting',
                'meeting' => $meeting,
                'user'=> $user,
                'unregister' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ];

            return response()->json($response,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
        $meeting->users()->detach();

        $response = [
            'msg' => 'User unregister for meeting',
            'meeting' => $meeting,
            'user' => 'tbd',
            'register' => [
                'href' => 'api/v1/meeting/registration',
                'method'=> 'POST',
                'params' => 'user_id, meeting_id'
            ]
            ];
            return response()->json($response,200);
    }
}
