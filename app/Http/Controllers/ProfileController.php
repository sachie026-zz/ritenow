<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Connection;
use App\Status;
use App\Connectrequest;
use DB;
use App\Helpers\RiteNowGlobal;

class ProfileController extends Controller
{
    //
	
	public function updateProfile()
	{
		try{
			
		}
		catch(Exception $ex){
		}
	}
	
	public function createProfile()
	{
		try{
			
		}
		catch(Exception $ex){
		}
	}
		
	
	public function getUserProfile(Request $request)
	{
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			$userid = isset($request->userid) ? $request->userid : null;
			//			$fbid = "9970016888";
			
			if($fbid == null || $userid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token


			$userProfileData = null;	
		//	return "1";
		$current_date=strtotime("now");
			$profileData = Status::where("fbid", $userid)->get(); 
			if(count($profileData) > 0){
				$userProfileData = 
				DB::table('profiles')
				->where('profiles.fbid', $userid)
				->join('posts', 'posts.fbid', '=', 'profiles.fbid')
				->get();
				$interval = strtotime($userProfileData[0]->expires_at) - $current_date;
				$userProfileData[0]->expires_at =  $interval <= 0 ? "expired" : $userProfileData[0]->expires_at;

			}
			else{
				$userProfileData = Profile::where("fbid", $userid)->get(); 

			}
			return count($userProfileData) > 0 ? $userProfileData : null;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	
	public function getCheckIfConnected(Request $request){
		try {
			$toid = isset($request->toid) ? $request->toid : null;
			$token = isset($request->token) ? $request->token : null;
			$fromid = isset($request->fromid) ? $request->fromid : null;
			
			if($fromid == null || $toid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fromid, $token))
				return 401;	// unauthorized or invalid token
			
			return RiteNowGlobal::getCheckIfConnected($toid, $fromid) ? "t" : "f";
			//	return 401;	// unauthorized or invalid token				
			
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getConnections(Request $request)
	{
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
//			$fbid = "9970016888";
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
//			$fbid = "123";
			$userConnections = Connection::where('fbid', $fbid)->get();
			$connectionsArray =  explode("->",$userConnections[0]->connections);
			$count = count($connectionsArray);
			//return $count;
			if($count > 0){
				$connections = Profile::whereIn('fbid', $connectionsArray)->get();
				return $connections;			
			}
			else{
				return null;
			}
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getRequests(Request $request)
	{
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
//			$fbid = "9970016888";
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			//$fbid = "123";
			$userRequests = Connectrequest::where('fbid', $fbid)->get();
			return $userRequests;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function updateSetting()
	{
		try{
			
		}
		catch(Exception $ex){
		}
	}
	
	public function createSetting()
	{
		try{
			
		}
		catch(Exception $ex){
		}
	}
}
