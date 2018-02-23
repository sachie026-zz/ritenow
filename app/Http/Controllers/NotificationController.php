<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Connectrequest;
use App\Connection;
use App\Notification;

class NotificationController extends Controller
{
    
	
	public function postSendConnectRequest(){
		try{
			$fbid = "9970016888";
			$from = "9";	//row id
			
			$userProfileData = Profile::where('fbid', $fbid)->get();
			
			$pName = $userProfileData[0]->name;
			$pPic = $userProfileData[0]->pic;
			
			
		//	$name = "vivek";
			//$picture = "assd.jpg";
			$present = Connectrequest::where('fbid', $fbid)->where('from', $from)->count() == 1 ? true : false;
			if(!$present){
				$request = new ConnectRequest;
		        $request->profile_name = $pName;
		        $request->fbid = $fbid;
		        $request->from = $from;	
		        $request->profile_pic = $pPic;					
		        $saved = $request->save();
				
				if($saved){
					//addNotification for new connect request
					$this->addNotification($fbid, $from, 1 , $pPic, $pName);
					$this->incrementRequestCountForId($userProfileData[0]->id);
					// add requests count
				}
		        return $saved ? 1 : 0;    			
			}
			return 2;
			
		}
		catch(Exception $ex){
			return -1;
		}
		
	}
	
	public function incrementRequestCountForId($id){
		$userProfile = Profile::find($id);
		$userProfile->increment('requests_count');
		$userProfile->save();	
	}
	
	public function incrementConnectionsCountForId($id){
		$userProfile = Profile::find($id);
		$userProfile->increment('connections_count');
		$userProfile->save();
	}
	
	public function decrementRequestCountForId($id){
		$userProfile = Profile::find($id);
		$userProfile->decrement('requests_count');
		$userProfile->save();
	}
	
	public function decrementConnectionsCountForId($id){
		$userProfile = Profile::find($id);
		$userProfile->decrement('connections_count');
		$userProfile->save();
	}
	
	public function postRejectRequest()
	{
		try{
			$requestId = "1";
			$row = ConnectRequest::where('id', $requestId)->get();
			$present = $row->count() == 1 ? true : false;	

			if($present){
				$otpRow = ConnectRequest::find($row[0]->id)	;
				$otpRow->delete();
				
				$userProfileData = Profile::where('fbid', $row[0]->fbid)->get();			
				$id = $userProfileData[0]->id;

				$this->decrementRequestCountForId($id);
			}
			else
				return 2;
			return 1;
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function postAcceptRequest()
	{
		try{
			$requestId = "2";
			$row = ConnectRequest::find($requestId);
			$present = $row->count() == 1 ? true : false;	

//			return $row->fbid;
			
			if($present){
				//$requestRow = ConnectRequest::find($row[0]->fbid);				
				$connectionData = Connection::find($row->fbid);
				//return $connectionData;
				if($connectionData->connections == null){
					$connectionData->connections = '->'.$row->from.'->';
				}
				else{
					$connectionData->connections = $connectionData->connections.$row->from.'->'; 
				}
				$connectionData->save();					
				$row->delete();		
				
				$userProfileData = Profile::where('fbid', $row->fbid)->get();			
				$id = $userProfileData[0]->id;

				$this->incrementConnectionsCountForId($id);
				$this->incrementConnectionsCountForId($row->from);
				//addnotification()		
				$this->addNotification($row->fbid, $row->from, 2 , $row->profile_pic, $row->profile_name);
				// increment connections count
			}
			else
				return 2;
			return 1;
		}
		catch(Exception $ex){
		}
	}
	
	public function addNotification($fbid, $from, $type, $pic, $name)
	{
		try{
			$row = Notification::where('fbid', $fbid)->where('from', $from)->where('type', $type)->get();
			$present = $row->count() == 1 ? true : false;	
			if($present)
					return 2;
			else{
				$notification = new Notification;
				$notification->fbid = $fbid;
				$notification->from = $from;
				$notification->type = $type;
				$notification->text = $type == 1 ? "You have new connect request": "You are now connected";
				$notification->profile_pic = $pic;
				$notification->profile_name = $name;
				return $notification->save();
			}				
		}
		catch(Exception $ex){
			return -1;
		}
	}
	
	public function getAllNotifications()
	{
		try{
			$fbid = "9970016888";
			$allNotifications = Notification::where('fbid', $fbid)->get();
			return $allNotifications;
		}
		catch(Exception $ex){
			return -1;
		}
	}

}
