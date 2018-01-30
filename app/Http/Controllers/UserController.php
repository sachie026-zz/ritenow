<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Otp;
use App\User;
use Hash;

class UserController extends Controller
{
    //
	
	public function checkAndAddNewUser(){
		try{
    		$mbl = "9970016888";
			$name = "Sachin Jadhav";
			$email = "jadhavsachin174@gmail.com";
			$pswd = "sachie";
			
    		$present = User::where('mbl', $mbl)->count() == 1 ? true : false;
    		if(!$present){
		        $User = new User;
		        $User->name = $name;
		        $User->mbl = $mbl;
		        $User->emailid = $email;		        
		        $User->password = Hash::make($pswd);
		        $saved = $User->save();
		        return $saved ? 1 : 0;    			
    		}
    		return 2;
    	}
    	catch(Exception $ex){
    		return -1;
    	}    
	}
	
	public function loginUser(){
		
	}
	
	public function sendOTP(Request $request){
		return "test";
		try{
			$mbl = '9970016888';
			$digits = '1234';

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
			//"2018-01-26 23:55:06";
			
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
