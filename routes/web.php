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

/////////////////// 

//code done //verified
Route::get('loginUser', 'UserController@checkAndAddNewUser');	

Route::get('signOutUser', 'UserController@signOutUser');

Route::get('getFriendsStatus', 'StatusController@getAllFriendsStatus');

//code done //verified
Route::get('getProfile', 'ProfileController@getUserProfile');

//code done // verified
Route::get('getNotifications', 'NotificationController@getAllNotifications');

Route::get('getSettings', 'ProfileController@getSettings');

//code done //verified
Route::get('getConnections', 'ProfileController@getConnections');

//code done //verified
Route::get('getRequests', 'ProfileController@getRequests');


//code done //verified
Route::get('postSendConnectionRequest', 'NotificationController@postSendConnectRequest');

//code done //verified
Route::get('postAddStatus', 'StatusController@postAddStatus');

//code done //verified
Route::get('postRemoveStatus', 'StatusController@postRemoveStatus');

//code done //verified
Route::get('postAcceptRequest', 'NotificationController@postAcceptRequest');

//code done //verified
Route::get('postRejectRequest', 'NotificationController@postRejectRequest');

Route::get('postUpdateSettings', 'ProfileController@postUpdateSettings');


//Route::get('signUp', 'UserController@signupUser');


/*	DB tables    DB tables   DB tables -  DB fields   DB fields   DB fields

*/

/*	UI page - fields    UI page - fields 

*/


/* API API API API

	login
	  
		check if user present
		check if first time login
		create profile entry
		notification [friend joind ritenow]
		remember logged in token
		
	
	---------------------------------------------------------------------------
	send connect request
	
		get friends on ritenow
		add request entry
		send request notification
		

	
	-------------------------------------------------------------------------
	get status

        get status for connected friends and not expired
		get profile
		get profile status
		remove current status
		add status
	
	----------------------------------------------------------------------------	
		
	
	get notifications
	
	
		get notifications for current user
		accept request
		cancel request
		
	------------------------------------------------------------------------------
	
	settings

		get user settings
		update setting
	*/