<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//nah jadi fungsi resource otomatis menentukan route yang akan di eksekusi

Route::group(['prefix'=>'v1'], function (){

    Route::resource('meeting','API\MeetingController',['except' => ['create','edit']]);

    Route::post('/user/register',[
        'uses' => 'AuthController@store'
    ]);
        
    Route::get('/user/signin',[
        'uses' => 'AuthController@signin'
    ]);
    
    // Route::post('/login','AuthController@login');

    Route::post('/auth','API\AuthController@authdata');
    Route::get('/logout','API\MeetingController@logout');


    Route::get('posts','API\MeetingController@posts');

    Route::match(['post'],'/login','AuthController@login');
    Route::resource('meeting/registration','RegisterController',[
        'only' => ['store','destroy']
    ]);
    Route::group(['middleware'=>'auth'],function(){

        Route::post('posts/create','AuthController@post_create');
     
    
        Route::post('testing_regist','API\MeetingController@testing_jwt_regist');
        Route::delete('delete/{id}','API\AuthController@delete_user');

    });
   
  
});


Route::get('foo',function(){
    return "hello world";
});

Route::match(['get','post','put','delete'],'/ourdata',function(){
    return "get data from all method API";
})->name('data');
    