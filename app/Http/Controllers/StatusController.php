<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use App\Postrecord;
use App\Connection;
use App\User;
use App\Profile;
use App\Chat;

use App\Helpers\RiteNowGlobal;
use DB;

class StatusController extends Controller
{
    //
	public function createVisibilityString(){
		
	}
	
	public function isTokenValid($mbl, $token){
		$userPresent = User::where('mbl', $mbl)->get();
			if($userPresent->count() <= 0)		// user not present
				return 2;
			
			$remember_token = $userPresent[0]->remember_token;
			if($remember_token != $token)
				return 3;
			
		return 1;				
	}
	
	
	public function sendPostInterest(){
		try{
			
		}
		catch(Exception $ex){
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

	public function getAllFriendsStatus(Request $request){
		try{


			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null )
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$connections = Connection::where('fbid', $fbid)->pluck("connections")[0]; 
			$current_date=strtotime("now");
			$allPosts = Status::whereDate("expires_at", ">=" ,date("Y-m-d", $current_date))->get();

			$usersStatus = [];
			
			foreach ($allPosts as $post) {
				$interval = strtotime($post->expires_at) - $current_date;
				//return $interval;
				if ( strpos($connections,  '->'.$post->fbid.'->') !== false &&  $interval >= 0 ) {
					$post->expires_at = $this->dateDiff(strtotime($post->expires_at) , $current_date, 2);
					//$post->updated_at = $interval;
					$post->interval = $interval*1000;

					$check = strpos($post->interested,  '->'.$fbid.'->') !== false;
					$post->isInterested = $check ? true : false;
					$post->mobile = Profile::where('fbid', $post->fbid)->pluck("mobile")[0];
					array_push($usersStatus, $post);
				}
			  }

			
			return $usersStatus;	
		}
		catch(Exception $ex){
			return $ex;
		}

	}

	public function postShowInterest(Request $request){
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null || $postId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$postRow = Status::find($postId);

			if($postRow != null)	
				$present = count($postRow) > 0 ? true : false;	
			else 
				$present = false;
			
			if($present){
				if($postRow->interested == null){
					$postRow->interested = '->'.$fbid.'->';
					$postRow->increment('interest_count');
				}
				else{
					if( strpos($postRow->interested,  '->'.$fbid.'->') !== false &&  strpos($postRow->interested,  '->'.$fbid.'->') >= 0)
					{
						return 3;
					}
					else{
						$postRow->interested  = $postRow->interested .$fbid.'->'; 
						$postRow->increment('interest_count');
					}
				}


				$postRow->save();		

				$userFcm = User::where('fbid', $postRow->fbid)->get();
				RiteNowGlobal::sendNotificationToDevice($userFcm[0]->fcm_token , "Your status got new interest");
				//return 1;
				//addnotification()		
			}
			else
				return 2;
			
			return 1;
		}
		catch(Exception $ex){
			return -1;
		}
	}

	public function getInterestedUsersForStatus(Request $request)
	{
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$postRow = Status::find($postId);

			if(count($postRow) == 0)
				return 2;

			if($postRow->interested == null)
				return [];


			$interestedUserArray =  explode("->",$postRow->interested);
			$count = count($interestedUserArray);

			if($count > 0){
				$users = Profile::whereIn('fbid', $interestedUserArray)->get();
				return $users;			
			}
			else{
				return [];
			}
		}
		catch(Exception $ex){
			return -1;
		}
	}

	public function postAddViewCount(Request $request){
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null || $postId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$postRow = Status::find($postId);

			if($postRow != null)	
				$present = count($postRow) > 0 ? true : false;	
			else 
				$present = false;
			
			if($present){
				if($postRow->viewers == null){
					$postRow->viewers = '->'.$fbid.'->';
					$postRow->increment('view_count');
				}
				else{
					if( strpos($postRow->viewers,  '->'.$fbid.'->') !== false &&  strpos($postRow->viewers,  '->'.$fbid.'->') >= 0)
					{
						return 3;
					}
					else{
						$postRow->viewers  = $postRow->viewers .$fbid.'->'; 
						$postRow->increment('view_count');
					}
				}

				$postRow->save();		
				$userFcm = User::where('fbid', $postRow->fbid)->get();
				RiteNowGlobal::sendNotificationToDevice($userFcm[0]->fcm_token , "Your status got new viewer");

				//return 1;
				//addnotification()		
			}
			else
				return 2;
			
			return 1;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getStatusViewers(Request $request)
	{
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			//return $postId;	
			
			$postRow = Status::find($postId);
			//return $postRow;

			if(count($postRow) == 0)
				return 2;

			if($postRow->viewers == null)
				return [];

			$viewersArray =  explode("->",$postRow->viewers);
			$count = count($viewersArray);

			if($count > 0){
				$users = Profile::whereIn('fbid', $viewersArray)->get();
				return $users;			
			}
			else{
				return null;
			}
		}
		catch(Exception $ex){
			return -1;
		}
	}

	public function postRemoveStatus(Request $request){
		try{

			$fbid = isset($request->fbid) ? $request->fbid : null;
			if(!$fbid)
				return 3;

			$token = isset($request->token) ? $request->token : null;
			if($token)
			{
				$userData = User::where('fbid', $fbid)->get();
				if($token != $userData[0]->remember_token)
					return 4;	//authentication problem 'token dosent match'		
			}
			else
				return 5;


			//$fbid = "9970016888";
			$row = Status::where('fbid', $fbid)->get();
    		$present = $row->count() == 1 ? true : false;
			if($present){
				$statusRow = Status::find($row[0]->id);
				//return $statusRow;
				
				
				$userProfileData = Profile::where('fbid', $fbid)->get();
				$userProfile = Profile::find($userProfileData[0]->id);
				$userProfile->current_status_text = null;
				$userProfile->save();


				$postrecord = new Postrecord;
				$postrecord->fbid = $statusRow->fbid;
				$postrecord->status = $statusRow->status;
				$postrecord->state = $statusRow->state;
				$postrecord->mobile =$statusRow->mobile;
				$postrecord->mood = $statusRow->mood;
				$postrecord->lattitude = $statusRow->lattitude;
				$postrecord->longitude = $statusRow->longitude;
				$postrecord->address = $statusRow->address;
				$postrecord->expires_at = $statusRow->expires_at;
				$postrecord->created_at = $statusRow->created_at;
				$postrecord->updated_at = $statusRow->updated_at;
				$postrecord->profile_name = $statusRow->profile_name;
				$postrecord->profile_pic = $statusRow->profile_pic;
				$postrecord->save();

				$statusRow->delete();

				Chat::where('postid', $row[0]->id)->delete();

				return 1;
			}
			return 2;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	
	public function postAddStatus(Request $request){
		try{
			
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$userData = null;
			if(!$fbid)
				return 3;

			$token = isset($request->token) ? $request->token : null;
			if($token)
			{
				$userData = User::where('fbid', $fbid)->get();
				if($token != $userData[0]->remember_token)
					return 4;	//authentication problem 'token dosent match'		
			}
			else
				return 5;
			
			$statusData = Status::where('fbid', $fbid)->get();
		//	return Status::find($statusData[0]->id);
			// if( $statusData->count())
			//	return 3;
				
			$latitude = isset($request->latitude) ? $request->latitude : null;
			$longitude = isset($request->longitude) ? $request->longitude : null;
			$address = isset($request->address) ? $request->address : null;
			$state = isset($request->state) ? $request->state : null;
			$mobile = isset($request->mobile) ? $request->mobile : null;
			$mood = isset($request->mood) ? $request->mood : null;
			$statusText = isset($request->status) ? $request->status : null;

			$duration = isset($request->duration) ? $request->duration : null;
			$durationUnit = isset($request->durationUnit) ? $request->durationUnit : null;

			//$next_staturday =strtotime("next Saturday");
			
		//	$expiry = date("Y-m-d H:i:s", $next_staturday);
		//return $duration."+"." ".$durationUnit.date("Y-m-d H:i:s", strtotime("+6 hours"))." ".date("Y-m-d H:i:s", $next_staturday);
			$expiry = date("Y-m-d H:i:s", strtotime("+".$duration." ".$durationUnit));
			
			$userProfileData = Profile::where('fbid', $fbid)->get();	
			
			$pName = $userProfileData[0]->name;
			$pPic = $userProfileData[0]->pic;

			$status = null;
			if($statusData->count() > 0)
				$status = Status::find($statusData[0]->id);
			else
				$status = new Status;
	
			$status->fbid = $fbid;
			$status->status = $statusText;
			$status->state = $state;
			$status->mobile = $mobile;
			$status->mood = $mood;
			$status->lattitude = $latitude;
			$status->longitude = $longitude;
			$status->address = $address;
			$status->expires_at = $expiry;
			$status->profile_name = $pName;
			$status->profile_pic = $pPic;
			
			$saved = $status->save();
			
			$userProfile = Profile::find($userProfileData[0]->id);
			$userProfile->current_status_text = $statusText;
			$userProfile->save();


			$userConnections = Connection::where('fbid', $fbid)->get();
			$connectionsArray =  explode("->",$userConnections[0]->connections);
			$count = count($connectionsArray);
			$userTokens = [];
			//return $count;
			if($count > 0){
				$userTokens = User::whereIn('fbid', $connectionsArray)->pluck('fcm_token');
				//return $userTokens;			
			}


			RiteNowGlobal::sendNotificationToDevice($userTokens->toArray(), "Someone added new status");

			return $saved ? 1 : 0;    			
    	}
    	catch(Exception $ex){
    		return $ex;
    	}
	}


	public function postAddStatusChatMessage(Request $request){
		// keep only 4 messages
		// delete status message on delete of status	
		//request params : status id, from id , message 
		//new params : message time , type [sent / recieve]
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			
			if($fbid == null || $postId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$msg = isset($request->token) ? $request->message : null;
			$fromid = isset($request->fromid) ? $request->fromid : null;
			$postRow = Status::find($postId);

			if($postRow != null)	
				$present = count($postRow) > 0 ? true : false;	
			else 
				$present = false;

			$fromUserData = Profile::where('fbid', $fbid)->get()[0]; 
						
			if($present){
				
				$chat = new Chat;
		
				$chat->fbid = $postRow->fbid;
				$chat->postid = $postId;
				$chat->fromname = $fromUserData->name;
				$chat->frompic = $fromUserData->pic;
				$chat->fromid = $fromid;
				$chat->message = $msg;
				$chat->type = ($postRow->fbid == $fbid) ? 'send' : 'recieve';
				$saved = $chat->save();
			}
			else
				return 2;
			
			return 1;
		}
		catch(Exception $ex){
			return -1;
		}
	}

	
	public function getChatList(Request $request){
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			//return $postId;	
			
			$postRow = Status::find($postId);
			//return $postRow;

			if(count($postRow) == 0)
				return 2;

			$statusChatList = DB::table('chats')->groupBy('fromid')->where('postid', $postId)->get();	

			return $statusChatList; 

		}
		catch(Exception $ex){
			return -1;
		}
	}

	public function getChats(Request $request){
		try{
			$postId = isset($request->postid) ? $request->postid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token

			//return $postId;	
			$fromid = isset($request->fromid) ? $request->fromid : null;
			
			$postRow = Status::find($postId);
			//return $postRow;

			if(count($postRow) == 0)
				return 2;

			$statusChats = Chat::where('fromid', $fromid)->where('postid', $postId)->get();	
			return $statusChats; 

		}
		catch(Exception $ex){
			return -1;
		}

	}

	public function getAllStatusForUser(){
		try{
			$user_id = "8";
			$allStatusForUser = Status::where('visibility', 'LIKE', '%->'.$user_id.'->%')->where('expires_at','<' , date('Y-m-d H:i:s'))-get();
			return $allStatusForUser;
		}
		catch(Exception $ex){
			return -1;
		}
			
	}
	
	
	
}
