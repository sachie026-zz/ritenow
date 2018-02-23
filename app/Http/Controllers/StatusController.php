<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use App\User;
use App\Profile;



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
	
	public function postAddStatus(){
		try{
    		$fbid = "9970016888";
			$statusText = "Sachin Jadhav";
			$expiry = date("Y-m-d H:i:s", time() + 30);
			
			$userProfileData = Profile::where('fbid', $fbid)->get();
			
			$pName = $userProfileData[0]->name;
			$pPic = $userProfileData[0]->pic;

			$status = new Status;
			$status->fbid = $fbid;
			$status->status = $statusText;
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
