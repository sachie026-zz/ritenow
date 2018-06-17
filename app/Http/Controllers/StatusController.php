<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use App\Connection;
use App\User;
use App\Profile;

use App\Helpers\RiteNowGlobal;


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

	public function getAllFriendsStatus(Request $request){
		try{


			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
			
			if($fbid == null )
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$connections = Connection::where('fbid', $fbid)->get(); 
			$allPosts = Status::all();

			$usersStatus = [];
			//return $connections[0]->fbid;
			foreach ($allPosts as $post) {
				if ( strpos($connections,  '->'.$post->fbid.'->') !== false) {
					array_push($usersStatus, $post);
				}
			  }

			
			return $usersStatus;	
		}
		catch(Exception $ex){
			return $ex;
		}

	}
	
	public function postRemoveStatus(){
		try{
			$fbid = "9970016888";
			$row = Status::where('fbid', $fbid)->get();
    		$present = $row->count() == 1 ? true : false;
			if($present){
				$statusRow = Status::find($row[0]->id)	;
				$statusRow->delete();
				
				$userProfileData = Profile::where('fbid', $fbid)->get();
				$userProfile = Profile::find($userProfileData[0]->id);
				$userProfile->current_status_text = null;
				$userProfile->save();
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
				return 3;
			
			$latitude = isset($request->latitude) ? $request->latitude : null;
			$longitude = isset($request->longitude) ? $request->longitude : null;
			$address = isset($request->address) ? $request->address : null;
			$state = isset($request->state) ? $request->state : null;
			$statusText = isset($request->status) ? $request->status : null;
			$expiry = date("Y-m-d H:i:s", time() + 30);
			
			$userProfileData = Profile::where('fbid', $fbid)->get();	
			
			$pName = $userProfileData[0]->name;
			$pPic = $userProfileData[0]->pic;

			$status = new Status;
			$status->fbid = $fbid;
			$status->status = $statusText;
			$status->state = $state;
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
			return $saved ? 1 : 0;    			
    	}
    	catch(Exception $ex){
    		return $ex;
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
