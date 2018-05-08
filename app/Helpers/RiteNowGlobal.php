<?php

namespace App\Helpers;


use App\Connection;
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
						
			$ids_array = explode("->",$ids);

			if(in_array($toid, $ids_array)){
				return true;
			}
			else{
				return false;
			}			
		}
		catch(Exception $ex){
			return -1;
		}
	}

	
}