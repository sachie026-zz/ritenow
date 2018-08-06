<?php

namespace App\Helpers;


use App\Connection;
use App\Connectrequest;
use App\User;


class RiteNowGlobal {


    public static function isValidToken($fbid, $userToken)
    {

        $userData = User::where('fbid', $fbid)->get();
		if($userToken != $userData[0]->remember_token)
			return false;
		else
			return true;

    }
	
	
	public static function getCheckIfConnected($toid, $fromid){
		try {
			$connections = Connection::find($fromid);
			$ids = $connections->connections;
						

			$requested = Connectrequest::where('fbid', $toid)->where('from', $fromid)->count();
			$accept = Connectrequest::where('fbid', $fromid)->where('from', $toid)->count();
		
			//$secondUserRequests = Connectrequest::where('fbid', $fromid)->get();

			$ids_array = explode("->",$ids);
		
			if(in_array($toid, $ids_array)){
				return 0; //"Connected";
			}
			
			if($requested > 0)
			{
				//return "Requested";
				return 2; //"Requested";
			}
			
			if($accept > 0){
				return 1; // "Accept";
			}
			else{
				return 3; //"send Connect Request";
			}			
		}
		catch(Exception $ex){
			return -1;
		}
	}

	
}