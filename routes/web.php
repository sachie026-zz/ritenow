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



Route::get('/getSearchedUser', 'UserController@getUsersForSearchText');

Route::get('/signOutUser', 'UserController@signOutUser');

Route::get('/getFriendsStatus', 'StatusController@getAllFriendsStatus');

//code done //verified
Route::get('/getProfile', 'ProfileController@getUserProfile');

Route::get('/checkIfConnected', 'ProfileController@getCheckIfConnected');

//code done // verified
Route::get('/getPostRecords', 'ProfileController@getPostHistory');

//code done // verified
Route::get('/getPublicPosts', 'ProfileController@getPublicPosts');


//code done // verified
Route::get('/getNotifications', 'NotificationController@getAllNotifications');

Route::get('/getSettings', 'ProfileController@getSettings');

//code done //verified
Route::get('/getConnections', 'ProfileController@getConnections');

//code done //verified
Route::get('/getInterestedUsers', 'StatusController@getInterestedUsersForStatus');

//code done //verified
Route::get('/getStatusViewers', 'StatusController@getStatusViewers');

//code done //verified
Route::get('/getRequests', 'ProfileController@getRequests');

//code done //verified
Route::get('/getFCMTokens', 'ProfileController@getFCMTokens');

//code done //verified
Route::get('/getChats', 'StatusController@getChats');

//code done //verified
Route::get('/getChatList', 'StatusController@getChatList');

//code done //verified
Route::get('/getProfileChatList', 'StatusController@getChatListForUser');


//code done //verified
Route::post('/loginUser', 'UserController@checkAndAddNewUser');	

//code done //verified
Route::post('/deleteUser', 'UserController@deleteUser');	


//code done //verified
Route::post('/postSendConnectionRequest', 'NotificationController@postSendConnectRequest');

//code remaining 
Route::post('/postDisConnect', 'NotificationController@postDisConnect');

//code done //verified
Route::post('/postAddStatus', 'StatusController@postAddStatus');

//code done //verified
Route::post('/postAddPublicStatus', 'StatusController@postAddPublicStatus');


//
Route::post('/postStatusChat', 'StatusController@postAddStatusChatMessage');

//code remaining 
Route::post('/postShowInterest', 'StatusController@postShowInterest');

//code remaining 
Route::post('/postAddViewCount', 'StatusController@postAddViewCount');

//code remaining 
Route::post('/postAddPublicStatusAction', 'StatusController@postAddPublicStatusAction');

//code done //verified
Route::post('/postRemoveStatus', 'StatusController@postRemoveStatus');

//code done //verified
Route::post('/postAcceptRequest', 'NotificationController@postAcceptRequest');

//code done //verified
Route::post('/postRejectRequest', 'NotificationController@postRejectRequest');

//code done //verified
Route::post('/postCancelRequest', 'NotificationController@postCancelRequest');


Route::post('/postUpdateProfile', 'ProfileController@postUpdateProfile');


Route::post('/postAddFCM', 'UserController@postAddFCMToken');


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