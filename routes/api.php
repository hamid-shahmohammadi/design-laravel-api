<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/',function (){
//    return response()->json(['message'=>'hello world']);
//});
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

// public routes
Route::get('me','User\MeController@getMe');

//designs
Route::get('designs','Designs\DesignController@index');
Route::get('designs/{id}','Designs\DesignController@findDesign');
Route::get('designs/slug/{slug}','Designs\DesignController@findBySlug');

//users
Route::get('users','User\UserController@index');
Route::get('user/{username}','User\UserController@findByUsername');
Route::get('users/{id}/designs','Designs\DesignController@getForUser');

// Teams
Route::get('teams/slug/{slug}','Teams\TeamsController@findBySlug');
Route::get('teams/{id}/designs','Designs\DesignController@getForTeam');

// Routes group for authenticated user only
Route::group(['middleware'=>['auth:api']],function (){
    Route::post('logout','Auth\LoginController@logout');
    Route::put('settings/profile','User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    //Upload Design
    Route::post('designs','Designs\UploadController@upload');
    Route::put('designs/{design}','Designs\DesignController@update');

    Route::delete('designs/{id}','Designs\DesignController@destroy');

    // Likes and Unlikes
    Route::post('designs/{id}/like','Designs\DesignController@like');
    Route::get('designs/{id}/liked','Designs\DesignController@checkIfHasLiked');


    // Comments
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{comment}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');

    // Teams
    Route::post('teams',"Teams\TeamsController@store");
    Route::get('teams/{id}',"Teams\TeamsController@findById");
    Route::get('teams',"Teams\TeamsController@index");
    Route::get('users/teams',"Teams\TeamsController@fetchUserTeams");
    Route::put('teams/{id}',"Teams\TeamsController@update");
    Route::delete('teams/{id}',"Teams\TeamsController@destroy");
    Route::delete('teams/{team_id}/users/{user_id}',"Teams\TeamsController@removeFromTeam");

    // Invitation
    Route::post('invitations/{teamId}',"Teams\InvitationsController@invite");
    Route::post('invitations/{id}/resend',"Teams\InvitationsController@resend");
    Route::post('invitations/{id}/respond',"Teams\InvitationsController@respond");
    Route::delete('invitations/{id}',"Teams\InvitationsController@destroy");

    // Chat
    Route::post('chats',"Chats\ChatController@sendMessage");
    Route::get('chats',"Chats\ChatController@getUserChats");
    Route::get('chats/{id}/messages',"Chats\ChatController@getChatmessages");
    Route::put('chats/{id}/markAsRead',"Chats\ChatController@markAsRead");
    Route::delete('messages/{id}',"Chats\ChatController@destroyMessage");

    // search
    Route::get('search/designs',"Designs\DesignController@search");
    Route::get('search/designers',"User\UserController@search");
});

// Routes group for guest only
Route::group(['middleware'=>['guest:api']],function (){
    Route::post('register','Auth\RegisterController@register');
    Route::get('verification/verify/{id}/{hash}','Auth\VerificationController@verify')->name('verification.verify');


    Route::post('verification/resend','Auth\VerificationController@resend');

    Route::post('login','Auth\LoginController@login');

    Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset','Auth\ResetPasswordController@reset');


});




//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
