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

//users
Route::get('users','User\UserController@index');

// Routes group for authenticated user only
Route::group(['middleware'=>['auth:api']],function (){
    Route::post('logout','Auth\LoginController@logout');
    Route::put('settings/profile','User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    //Upload Design
    Route::post('designs','Designs\UploadController@upload');
    Route::put('designs/{design}','Designs\DesignController@update');

    Route::delete('designs/{id}','Designs\DesignController@destroy');

    // Comments
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{comment}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');
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
