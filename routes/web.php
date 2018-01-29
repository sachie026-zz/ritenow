<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//	return str_random(16);
//	return $date = date('Y-m-d H:i:s') == date("Y-m-d H:i:s", time() + 10) ? "t" : "f";
//	return $date = date('Y-m-d H:i:s')."        ".date("Y-m-d H:i:s", time() + 60*60*24);

			
});

Route::get('signupUser', 'UserController@checkAndAddNewUser');

Route::get('sendOTP', 'UserController@sendOTP');

Route::get('verifyOTP', 'UserController@verifyOTP');

//Route::get('signUp', 'UserController@signupUser');
