<?php

namespace App\Http\Controllers\API;

use App\Models\PollAnswered;
use App\Models\PollOption;
use App\User;
use App\Models\Poll;
use App\Models\PollOptions;
use App\Models\PollAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth,Response,DB,Hash,Session,Redirect,Validator;

class PollController extends Controller
{
    
    	public function PollList(Request $request){
		$sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
		$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
		
		$formData	= $request->all();
		$detail = (object) null; 
		$response	= array();

            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;

					 $userId 		=  $user_id;
					$eventid =  $request->eventid ;
					
					$polllist  	=  Poll::with('polloption')->where('eventid',$eventid)->get();

                        if($polllist->isEmpty()){
						$response=array(
						    'status'=>0,
						   	'message'=> 'لا يوجد مواضيع للتصويت',
						    'data'=> $polllist);

						}else{
						$response=array(
						    'status'=>1,
						    'message'=> 'تم بنجاح',
						    'data'=> $polllist);

						}
				
                        

			

	    return ($response);
					
	}
	
	
	public function PollOptionsList(Request $request){
		$sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
		$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
		
		$formData	= $request->all();
		$detail = (object) null; 
		$response	= array();



        $user = Auth::guard('customers_api')->user();
        $user_id = $user->id;


					$pollid =  $request->pollid ;
					$pollOptionlist  	=  PollOption::with("poll")->where('poll_id',$pollid)->get();
						$response = array(
							'status' 	=> 1,
							'message'	=> "تم بنجاح",
							'data' => $pollOptionlist,
						);


			

	    return ($response);
					
	}
	
	public function sendPollAnswer(Request $request){
	    	
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'contact_us.created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
        $user = Auth::guard('customers_api')->user();
        $user_id = $user->id;

                $pollid = $request->pollid;
                $pollOptionID = $request->pollOptionID;

			        $obj 					=  new PollAnswered;
					$obj->poll_id			=  $pollid;
					$obj->user_id			=  $user_id;
					$obj->poll_options_id	=  $pollOptionID;
					$obj->save();

				$response=array(
				    'status'=>1,
				    'message'=>"تم التصويت بنجاح");
				return Response::json($response);
				

	}
	
	        public function checkUserPoll(Request $request){
	            $pollid = $request->pollid;
				$user_id = $request->user_id;

				$result= PollAnswered::where('poll_id' , $pollid)
				->where('user_id' , $user_id)
				->get();
								          //  return Response::json($result);

					if(Count($result) == 1){
					    	$pollanswer= PollAnswered::where('poll_id' , $pollid)
			            	->get();
			            	
			            	$response=array(
			            	    'status'=>1,
			            		'message'	=> "تم التصويت بنجاح",
			            	    'data'=>$pollanswer);
				            return Response::json($response);
					}else{
					    $response=array(
					        'status'=>0,
					        'message'=> '');
					        
				            return Response::json($response);
					}
				
	        }
	        

    
}