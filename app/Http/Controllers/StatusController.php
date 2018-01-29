<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;


class StatusController extends Controller
{
    //
	public function createVisibilityString(){
		
	}
	
	public function addStatus(){
		try{
    		$mbl = "9970016888";
			$statusText = "Sachin Jadhav";
			$expiry = date("Y-m-d H:i:s", time() + 30);
			$visibility = "->1->2->";
			
			//$date = date('Y-m-d H:i:s');
			//date("m/d/Y h:i:s a", time() + 30);
			
			$row = Status::where('mbl', $mbl)-get();
    		$present = $row->count() == 1 ? true : false;
			if($present){
				$statusRow = Status::find($row[0]->id)	;
				$statusRow->delete();
			}
	
			$status = new Status;
			$status->mbl = $mbl;
			$status->statusText = $statusText;
			$status->expiry = $expiry;
			$status->visibility = $visibility;
			
			$saved = $status->save();
			return $saved ? 1 : 0;    			
//    		return 2;
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
