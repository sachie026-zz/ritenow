<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Connection;
use App\User;
use App\Postrecord;
use App\Status;
use App\Publicpost;
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
//				return $userProfileData;
				$interval = strtotime($userProfileData[0]->expires_at) - $current_date;
//				$userProfileData[0]->expires_at =  $interval <= 0 ? "Expired" : $this->dateDiff(strtotime($userProfileData[0]->expires_at) , $current_date, 2);
				$userProfileData[0]->expires_at =  $interval <= 0 ? "Expired" : strtotime($userProfileData[0]->expires_at);
				$userProfileData[0]->created_at = $current_date;
				$userProfileData[0]->updated_at = $interval*1000;
				$userProfileData[0]->mobile = Profile::where("fbid", $userid)->pluck('mobile')[0];
			}
			else{
				$userProfileData = Profile::where("fbid", $userid)->get(); 

			}

			$userRecordsCount = Postrecord::where('fbid', $userid)->count();
			$userProfileData[0]->history_count = $userRecordsCount;
			$userProfileData[0]->user_connect_status = ($fbid == $userid)  ? -1 : RiteNowGlobal::getCheckIfConnected($userid, $fbid) ;

			if($userProfileData[0]->user_connect_status == 1)
			{
				$requested = Connectrequest::where('fbid', $fbid)->where('from', $userid)->get()[0];
				$userProfileData[0]->request_id = $requested->id;
			}
			if($userProfileData[0]->user_connect_status == 2)
			{
				$accept = Connectrequest::where('fbid', $userid)->where('from', $fbid)->get()[0];
				$userProfileData[0]->request_id = $accept->id;
			}
		

			return count($userProfileData) > 0 ? $userProfileData : null;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function dateDiff($time1, $time2, $precision = 6) {
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
		  $time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
		  $time2 = strtotime($time2);
		}
	
		// If time1 is bigger than time2
		// Then swap time1 and time2
		if ($time1 > $time2) {
		  $ttime = $time1;
		  $time1 = $time2;
		  $time2 = $ttime;
		}
	
		// Set up intervals and diffs arrays
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();
	
		// Loop thru all intervals
		foreach ($intervals as $interval) {
		  // Create temp time from time1 and interval
		  $ttime = strtotime('+1 ' . $interval, $time1);
		  // Set initial values
		  $add = 1;
		  $looped = 0;
		  // Loop until temp time is smaller than time2
		  while ($time2 >= $ttime) {
			// Create new temp time from time1 and interval
			$add++;
			$ttime = strtotime("+" . $add . " " . $interval, $time1);
			$looped++;
		  }
	 
		  $time1 = strtotime("+" . $looped . " " . $interval, $time1);
		  $diffs[$interval] = $looped;
		}
		
		$count = 0;
		$times = array();
		// Loop thru all diffs
		foreach ($diffs as $interval => $value) {
		  // Break if we have needed precission
		  if ($count >= $precision) {
			break;
		  }
		  // Add value and interval 
		  // if value is bigger than 0
		  if ($value > 0) {
			// Add s if value is not 1
			if ($value != 1) {
			  $interval .= "s";
			}
			// Add value and interval to times array
			$times[] = $value . " " . $interval;
			$count++;
		  }
		}
	
		// Return string with times
		return implode(", ", $times);
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

	public function getPublicPosts(Request $request){
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null )
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			$publicposts = Publicpost::orderBy('created_at', 'desc')->get();
			return $publicposts;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getPostHistory(Request $request){
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			$userid = isset($request->userid) ? $request->userid : null;
			
			if($fbid == null || $userid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			$userRecords = Postrecord::where('fbid', $userid)->get();
			return $userRecords;
		}
		catch(Exception $ex){
			return -1;
		}
	}

	public function getFCMTokens(Request $request)
	{
		try{
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			$userConnections = Connection::where('fbid', $fbid)->get();
			$connectionsArray =  explode("->",$userConnections[0]->connections);
			$count = count($connectionsArray);
			//return $count;
			if($count > 0){
				$userTokens = User::whereIn('fbid', $connectionsArray)->pluck('fcm_token');
				return $userTokens;			
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
	
	public function postUpdateProfile(Request $request)
	{
		try{

			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			//$status = null;
			$recordRow = Profile::where('fbid',$fbid)->get();
			
			if($recordRow->count() <= 0)
				return 2;

			$livesin = isset($request->livesin) ? $request->livesin : null;
			$fromaddress = isset($request->fromaddress) ? $request->fromaddress : null;
			$mbl = isset($request->mobile) ? $request->mobile : null;

			$record = Profile::find($recordRow[0]->id);	
			$record->lives_in = $livesin;
			$record->from_address = $fromaddress;
			$record->mobile = $mbl;
			$saved = $record->save();
			return $saved ? 1 : 0;    
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
