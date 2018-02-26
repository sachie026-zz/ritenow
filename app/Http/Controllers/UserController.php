<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Otp;
use App\User;
use App\Profile;
use App\ConnectRequest;
use App\Connection;
use Hash;

class UserController extends Controller
{
    //
	
	public function createNewUserProfile($fbid, $name, $picture){
		try{
    		$present = Profile::where('fbid', $fbid)->count() == 1 ? true : false;
    		if(!$present){
		        $profile = new Profile;
		        $profile->name = $name;
		        $profile->fbid = $fbid;
		        $profile->pic = $picture;		        
		        $saved = $profile->save();
//		        return $saved ? 1 : 0;    			
    		}
//    		return 2;
    	}
    	catch(Exception $ex){
    		return -1;
    	}		
	}
	

	public function createNewConnectionEntry($fbid){
		try{
			$present = Connection::where('fbid', $fbid)->count() == 1 ? true : false;
    		if(!$present){
		        $connection = new Connection;
		        $connection->fbid = $fbid;
		        $saved = $connection->save();
					}
		}
		catch(Exception $ex){
		}
	}

	
	public function checkAndAddNewUser(Request $request){
		try{
			//return isset($request->fbid);
//			return $request;
		//	return $request == null || $request === [] ? 't' : 'f';
    		$isfbid = isset($request->fbid);
			if(!$isfbid)
				return 3;
		//return $fbid ? 't' : 'f';
			$fbid = $request->fbid;
			$name = "Sachin Jadhav";
			$email = "jadhavsachin174@gmail.com";
			$picture = "asdf.jpg";
			$token = "asdfghjkl";
			
    		$present = User::where('fbid', $fbid)->count() == 1 ? true : false;
    		if(!$present){
		        $User = new User;
		        $User->name = $name;
		        $User->fbid = $fbid;
		        $User->emailid = $email;		        
		        $saved = $User->save();
				if($saved == 1){
					$this->createNewUserProfile($fbid, $name, $picture);
					$this->createNewConnectionEntry($fbid);
				}	
		        return $saved ? 1 : 0;    			
    		}
    		return 2;
    	}
    	catch(Exception $ex){
    		return -1;
    	}    
	}
	
	public function signinUser(){
		try{
			$mbl = '9970016888';
			$pswd = 'secret';
			$token = 'token';
			
			$userPresent = User::where('mbl', $mbl)->get();
			if($userPresent->count() <= 0)		// user not present
				return 2;
 
			$hashedPassword = Hash::make($userPresent[0]->password);
	
			if (Hash::check($pswd, $hashedPassword))
			{
				$user = User::find('id', $userPresent[0]->id);
				$user->remember_token = $token;				
				$saved = $user->save();
				if($saved)
					return 1;
				else
					return 0;	
			}
			else{
				return 3;
			}
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	
	
	
	public function signOutUser(){
		try{
			$mbl = '9970016888';
			$token = 'token';
			$userPresent = User::where('mbl', $mbl)->get();
		
			if($userPresent->count() > 0)
			{
				$user = User::find('id', $userPresent[0]->id);
				$user->remember_token = NULL;				
				$saved = $user->save();

				if($saved)
					return 1;
				else
					return 0;	

			}
			else{
				return 2;
			}
		}
		catch(Exception $ex){
			return -1;
		}
	}
	

	public function sendOTP(Request $request){
		try{
			$mbl = '9970016888';
			$digits = '1234';

			$userPresent = User::where('mbl', $mbl)->count() == 1 ? true : false;
			if($userPresent)
				return 2;

			$row = Otp::where('mbl', $mbl)->get();
			$present = $row->count() == 1 ? true : false;	

			if($present){
				$otpRow = Otp::find($row[0]->id)	;
				$otpRow->delete();
			}
			$otp = new Otp;
			$otp->mbl = $mbl;
			$otp->digits = $digits;
			$otp->expires_at = date("Y-m-d H:i:s", time() + 30);
			
			$saved = $otp->save();
			return $saved ? $digits : 0;
		}
		catch(Exception $ex){
			return -1;
		}				
	}

	
	public function verifyOTP(){
		try{
			$mbl = '9970016888';
			$digits = '1234';
			
			$userPresent = User::where('mbl', $mbl)->count() == 1 ? true : false;
			if($userPresent)
				return 2;
			
			$row = Otp::where('mbl', $mbl)->where('expires_at', '>', date("Y-m-d H:i:s"))->get();
			$present = $row->count() == 1 ? true : false;	
			
			if($present){
				if($row[0]->digits === $digits)
					return 1;
				else
					0;
			}
			return 0;
		}
		catch(Exception $ex){
			return -1;
		}				
	}	
}
