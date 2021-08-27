<?php

namespace App\Http\Controllers\API;

use App\Models\ContactU;
use App\Models\SiteAddressesDe;
use App\Models\TermsCondition;
use App\Models\WaysUse;
use App\User;
use App\Models\Faculty;
use App\Models\FacultyDescription;
use App\Models\Notification;
use App\Models\CancelEventSub;
use App\Models\Faq;
use App\Models\FaqDescription;
use App\Models\Booking;
use App\Models\TermsConditions;
use App\Models\SponsorPlatinum;
use App\Models\WayUse;
use App\Models\WhoWeAre;
use App\Models\SiteAddressDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth,Response,DB,Hash,Session,Redirect,Validator;

class OtherController extends Controller
{
    
	public function faq(Request $request)	
	{
		
			$formData=$request->all();

				$language_id=1;


				
				$result=FaqDescription::join('faqs as u','parent_id','=','u.id')
										->where('u.is_active',1)
										->where('language_id',$language_id)
										->select('parent_id','language_id','faq_descriptions.question','faq_descriptions.answer','faq_descriptions.created_at','faq_descriptions.updated_at','u.is_active')
										->get();
				
				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);
				

	}
	
		public function sponsorplatinumList(Request $request)	
	{
	
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
	    		/*
			$messages=array(
						'user_id.required'			=> 'Please Enter UserID',
						'session_token.required'	=> 'Please Enter Session ID',

			);

			$validator=validator::make(
						$request->all(),
						array(
							'user_id'		=> 'required',
							'session_token'	=> 'required',
						),$messages
					);

			if($validator->fails())
			{

				$allErrors ='';
				foreach ($validator->errors()->all() as $message) {
					$allErrors=$message;
					break;
				}

				$response=array('status'=>0,'message'=>$allErrors);
				return Response::json($response);
				die();
			}
			else
			{
				
				$user_id = $formData['user_id'];
				$session_token =  $formData['session_token'];

				$checkUserSession = $this->verifyUserSession($user_id, $session_token);
				if(is_array($checkUserSession)){
					return  Response::json($checkUserSession);
					die();
				}
*/
				
				$result=SponsorPlatinum::where('status',1)->get();
			//	$result=SponsorPlatinum::all;
				$result= SponsorPlatinum::orderBy($sort_by, $sort_type)->get();

				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);
				
		//	}
	}
	
	
			public function whoweareList(Request $request)	
	{
		
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
	    	
			 $result= WhoWeAre::orderBy($sort_by, $sort_type)->get();

				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);
				

	}
	
	
			public function NotificationsList(Request $request)	
	{
		
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
				 $user_event_details=Booking::where('user_id',$user_id)->select('event_id')->get();

                $eventid = [];
				foreach($user_event_details as $key => $value) {
		          $eventid[] = $user_event_details[$key]->event_id;
                }

                $result= Notification::where('userid',  $user_id)
                ->orWhere('alluser', '=', 1)
                    ->orWhereIn('eventid',$eventid)
				->orderBy($sort_by, $sort_type)->get();
				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);
	}
	
	
				public function WayUseList(Request $request)	
	{
		
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

			
                $result= WaysUse::orderBy($sort_by, $sort_type)->get();

				//$result=Notifications::all();
				
				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);
				

	}

	public function contactus(Request $request)	
	{
		
			$formData=$request->all();
			$sort_by     	= $request->sort_by ? $request->sort_by : 'contact_us.created_at';
	    	$sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
 				$result=ContactU::where('user_id' , $user_id)->orderBy($sort_by, $sort_type)->get();
				
				$response=array('status'=>1,'data'=>$result);
				return Response::json($response);

	}

	public function Addcontactus(Request $request)	
	{   
		$formData	= $request->all();
		$detail 	= new \stdClass();
		 $response	= array();
		 $messages = array(
					'name.required' 		=> "Please Enter Name.",
				//	'email.required' 		=> "Please Enter Email",
					'subject.required' 		=> "Please Enter Subject",
					'mobile.required' 		=> "Please Enter Mobile",
					
				);
				
			$validator = Validator::make(
					$request->all(),
					array(
						'name' 		=> 'required',
					//	'email'		=> 'required|email',
						'subject'	=> 'required',
						'mobile'	=> 'required',
						
					), $messages
				);
				
		     if ($validator->fails()) 
		     {    
		     	$allErrors =  '';
		     	foreach ($validator->errors()->all() as $message){
							$allErrors =  $message; 
							break;
					}

                $response	=	array(
					'status' 	=> 0,
					'message'	=> $allErrors,
				
				);
			
			}else{ 
			    $user_id    =$request->user_id;

				$name    = $request->name;
			//	$email   = $request->email;
				$subject = $request->subject;
				$mobile  = $request->mobile;
				$message  = $request->message;
				$date    = date("Y-m-d H:i:s");
				ContactU::create(
				    ['user_id' => $user_id,
				    'name' =>$name,
				   // 'email' => $email,
				    'subject' => $subject,
				    'mobile' => $mobile,
				    'created_at' => $date,
				    'updated_at' => $date]);
			
				$success = 1;
				$errors	= 'تم إدراج البيانات بنجاح';

				$response	=	array(
				'status' 	=>$success,
				'message'	=> $errors,
				);
					
				}				
		
		     return Response::json($response);
	}

	
		public function canceleventsub(Request $request)	
	{   
		$formData	= $request->all();
		$detail 	= new \stdClass();
		
		 $response	= array();
		 $messages = array(
					'eventid.required' 		=> "Please select Event.",
				
				);
				
			$validator = Validator::make(
					$request->all(),
					array(
						'event_id' 		=> 'required',
					
					), $messages
				);
				
		     if ($validator->fails()) 
		     {    
		     	$allErrors =  '';
		     	foreach ($validator->errors()->all() as $message){
							$allErrors =  $message; 
							break;
					}

                $response	=	array(
					'status' 	=> 0,
					'message'	=> $allErrors,
				
				);
			
			}else{

                 $user = Auth::guard('customers_api')->user();
                 $user_id = $user->id;
			    
				$eventid    = $request->event_id;
				$bookid    = $request->book_id;
				
				$date    = date("Y-m-d H:i:s");


				CancelEventSub::insert(
				    ['user_id' => $user_id,
				    'event_id' =>$eventid,
				    'status' => 0,
				    'book_id' => $bookid,
				    'created_at' => $date,
				    'updated_at' => $date]);
			

				$response	=	array(
				'status' 	=> 1,
				'message'	=> 'تم  إرسال الطلب بنجاح',
				);
					
				}				
		
		     return Response::json($response); die;
	}
	
	public function TermsCondition(Request $request)	
	{   
		$formData=$request->all();
			

		
					
				$result=TermsCondition::get();

				$response=array('status'=>1,'data'=>$result);

				return Response::json($response);

	}

	public function facultyList(Request $request)	
	{   
		$formData=$request->all();
			

				$language_id=1;
				

				
				$result=FacultyDescription::join('faculty as u','parent_id','=','u.id')
										->where('language_id',$language_id)
										->select('parent_id','language_id','faculty_descriptions.full_name','faculty_descriptions.created_at','faculty_descriptions.updated_at','u.status')
										->get();
										
				foreach ($result as $key => $value) {
                        
                         $result[$key]['parent_id'] = (int)$value['parent_id'];
                    
                }
				$response=array('status'=>1,'detail'=>$result);
				return Response::json($response);
				

	}

	public function GetSiteAddress(Request $request)	
	{   
		$formData=$request->all();


				$language_id=1;
				

				$result=SiteAddressesDe::join('site_addresses as u','parent_id','=','u.id')
										->where('language_id',$language_id)
										->select('parent_id','language_id','site_addresses_des.content','site_addresses_des.created_at','site_addresses_des.updated_at')
										->first();
				
										if(empty($result))
										{
											$response=array('status'=>0,'message'=>'No Data found.');
											return Response::json($response);
										}
										else
										{
											$response=array('status'=>1,'message'=>$result->content);
												return Response::json($response);
										}
				
				

	}
	
		private function verifyUserSession($user_id, $session_token){

				$detail = (object) null; 
				$user_data = DB::table('users')->where('id',$user_id)->count();
				$sessions_data = DB::table('sessions')->where('user_id',$user_id)->first();

				if(empty($user_data)){
					$status = 0;
					$message = "Invalid User Access.";
				}elseif(empty($sessions_data)){
					$status = -1;
					$message = "Invalid Session Token.";
				}elseif($sessions_data->session_token != $session_token){
					$status = -1;
					$message = "Session Token Did Not Match.";
				}else{
					$status = 1;
					$message = '';
				}


				if($status == 0 || $status == -1){
					return $response = array(
							'status' => $status,
							'message' => $message,
							'detail' => $detail
						);
				}else{
					return 1;
				}

	}
    
}