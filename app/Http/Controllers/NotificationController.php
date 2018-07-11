<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Connectrequest;
use App\Connection;
use App\Notification;

use App\Helpers\RiteNowGlobal;


class NotificationController extends Controller
{
	
	public function postSendConnectRequest(Request $request){
		try{
			
			//return $userCtrl->isValidToken("123", "sj");
			$from = isset($request->fromid) ? $request->fromid : null;			
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
//			$fbid = "9970016888";
			
			if($fbid == null || $from == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($from, $token))
				return 401;	// unauthorized or invalid token
			
			//$fbid = "123";
			//$from = "23456";	
			
			$userProfileData = Profile::where('fbid', $fbid)->get();
			$sendersProfileData = Profile::where('fbid', $from)->get();
			
			//return $sendersProfileData;
			if(count($userProfileData) == 0 || count($sendersProfileData) == 0)
				return 3;
			
			$pName = $sendersProfileData[0]->name;
			$pPic = $sendersProfileData[0]->pic;
			

			$present = Connectrequest::where('fbid', $fbid)->where('from', $from)->count() == 1 ? true : false;
			if(!$present){
				//return "np";
				$crequest = new Connectrequest;
		        $crequest->profile_name = $pName;
		        $crequest->fbid = $fbid;
		        $crequest->from = $from;	
		        $crequest->profile_pic = $pPic;					
		        $saved = $crequest->save();
				
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
		//$profile = Profile::where('fbid', $id)->get();
		//return "row id ".$profileId;
		
		$userProfile = Profile::find($id);
		$userProfile->increment('connections_count');
		$userProfile->save();
	}
	
	public function decrementRequestCountForId($id){
		
		$userProfile = Profile::find($id);
		$userProfile->decrement('requests_count');
		$userProfile->save();
	}
	
	
	
	public function postRejectRequest(Request $request)
	{
		try{
			
			$requestId = isset($request->requestid) ? $request->requestid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
//			$fbid = "9970016888";
			
			
			if($fbid == null || $requestId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			//$requestId = 7;
			$row = Connectrequest::where('id', $requestId)->get();
			$present = $row->count() == 1 ? true : false;	

			if($present){
				$otpRow = Connectrequest::find($row[0]->id)	;
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

	public function decrementConnectionsCountForId($id){
		$userProfileData = Profile::where('fbid', $id)->get();

		$userProfile = Profile::find($userProfileData[0]->fbid);
		$userProfile->decrement('connections_count');
		$userProfile->save();
	}

	public function postDisConnect(Request $request){
		try{
			$removeUserId = isset($request->userid) ? $request->userid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
	//			$fbid = "9970016888";
			
			
			if($fbid == null || $removeUserId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			$fbUser = Connection::where('fbid',$fbid);
			$removeUser = Connection::where('fbid', $removeUserId);

			if(count($fbUser) <= 0 && count($removeUser) <= 0)
				return 2;
	
			//return $fbUser;	
			$connectionData = Connection::find($fbid);
			if($connectionData->connections == '->'.$removeUserId.'->'){
				$connectionData->connections = null;
			}
			else{
				$connectionData->connections = str_replace('->'.$removeUserId.'->', "->", $connectionData->connections); 
			}
			$connectionData->save();
			
			$connectionData = Connection::find($removeUserId);
			if($connectionData->connections == '->'.$fbid.'->'){
				$connectionData->connections = null;
			}
			else{
				$connectionData->connections = str_replace('->'.$fbid.'->', "->", $connectionData->connections); 
			}
			$connectionData->save();

			$this->decrementConnectionsCountForId($fbid);
			$this->decrementConnectionsCountForId($removeUserId);

			return 1;
	
		}
		catch(Exception $ex){
			return -1;
		}

	}
	
	public function postAcceptRequest(Request $request)
	{
		try{
			$requestId = isset($request->requestid) ? $request->requestid : null;
			$fbid = isset($request->fbid) ? $request->fbid : null;
			$token = isset($request->token) ? $request->token : null;
//			$fbid = "9970016888";
			
			
			if($fbid == null || $requestId == null)
				return 5;
			
			if(!RiteNowGlobal::isValidToken($fbid, $token))
				return 401;	// unauthorized or invalid token
			
			
			//$requestId = 8;
			$row = Connectrequest::find($requestId);
		//	return count($row);
		//return $row->from;
			if($row != null)	
				$present = count($row) > 0 ? true : false;	
			else 
				$present = false;

//			return $row->fbid;
			
			if($present){
				//$requestRow = ConnectRequest::find($row[0]->fbid);				
				$connectionData = Connection::find($row->fbid);
				if($connectionData->connections == null){
					$connectionData->connections = '->'.$row->from.'->';
				}
				else{
					$connectionData->connections = $connectionData->connections.$row->from.'->'; 
				}
				$connectionData->save();	
		
				
				$connectionData = Connection::find($row->from);
				if($connectionData->connections == null){
					$connectionData->connections = '->'.$row->fbid.'->';
				}
				else{
					$connectionData->connections = $connectionData->connections.$row->fbid.'->'; 
				}
				$connectionData->save();	
				
				
				$userProfileData = Profile::where('fbid', $row->fbid)->get();							
				$id = $userProfileData[0]->id;

				$senderProfileData = Profile::where('fbid', $row->from)->get();							
				$senderId = $senderProfileData[0]->id;
				//return $row.$id;
				
				//return $profile = Profile::where('fbid', $id)->get();
				//return $row->from.Profile::find($row->from);
				$this->incrementConnectionsCountForId($id);
				$this->incrementConnectionsCountForId($senderId);
				$this->decrementRequestCountForId($id);
				//addnotification()		
				$this->addNotification($row->fbid, $row->from, 2 , $row->profile_pic, $row->profile_name);
				// increment connections count
				$row->delete();		
			}
			else
				return 2;
			return 1;
		}
		catch(Exception $ex){
			return -1;
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
	
	public function getAllNotifications(Request $request)
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

			$allNotifications = Notification::where('fbid', $fbid)->get();
			return $allNotifications;
		}
		catch(Exception $ex){
			return -1;
		}
	}

}
