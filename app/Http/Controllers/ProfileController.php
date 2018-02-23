<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Connection;
use App\ConnectRequest;


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
	
	public function getUserProfile()
	{
		try{
			$fbid = "9970016888";
			$userProfileData = Profile::where('fbid', $fbid)->get();
			return $userProfileData;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getConnections()
	{
		try{
			$fbid = "9970016888";
			$userConnections = Connection::where('fbid', $fbid)->get();
			$connectionsArray =  explode("->",$userConnections[0]->connections);
			$count = count($connectionsArray);
			if($count > 0){
				$connections = Profile::whereIn('id', $connectionsArray)->get();
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
	
	public function getRequests()
	{
		try{
			$fbid = "9970016888";
			$userRequests = ConnectRequest::where('fbid', $fbid)->get();
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
