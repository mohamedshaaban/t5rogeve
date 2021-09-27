<?php

namespace App\Http\Controllers\API;

use App\Models\Bookings;
use App\Models\Customer;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\User;
use App\Models\Ceremony;
use App\Models\Booking;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth,Response,DB,Hash,Session,Redirect,Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use function PHPUnit\Framework\isNull;

class CeremonyController extends Controller
{

    public function ceremonyList(Request $request){
        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';
        $formData	= $request->all();
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
             $filter_by = $formData['filter_by'];
            $filter_type = $formData['filter_type'];
                $userdetail  	=  Customer::where('id',$user_id)->first();
                if(!empty($search)){

                    $ceremonies = Ceremony::with('poll','poll.polloption')->where('status','1')
                        ->where('name','like', '%' . $search . '%')
                        ->where('status','1')
                        ->get();

                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );
                }else{

                    if($filter_by=='gender'){
                        $ceremonies = Ceremony::with('poll','poll.polloption')->where('status','1')
                            ->whereRaw("FIND_IN_SET($filter_type,ceremony_for)")
                            ->get();

                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else if($filter_by=='faculty'){

                        $ceremonies = Ceremony::with('poll','poll.polloption')->where('status','1')
                            ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                            ->get();


                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else{
                        $ceremonies = Ceremony::with('poll','poll.polloption')->where('status','1')
                            ->where(function ($query) use ($userdetail) {
                                $query->whereDate('date', '>=' ,date("Y-m-d"));

                            })->orderBy($sort_by, $sort_type)->paginate(100);
                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );
                    }
                }

        return ($response);
        //return Response::json_encode( $response, JSON_NUMERIC_CHECK );




    }


    public function bookCeremonySeats(Request $request){
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(
            'ceremony_id' 	=> "Please Enter Ceremony Id",
            'price' 		=> "Please Enter Price",
            'discount' 		=> "Please Enter Discount",
            'final_price' 	=> "Please Enter Final Price",
            'seats' 		=> "Please Enter No. Of Seats",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'ceremony_id' 		=> 'required',
                'price' 			=> 'required',
                'discount' 			=> 'required',
                'final_price' 		=> 'required',
                'seats' 			=> 'required',
            ), $messages
        );
        if ($validator->fails()){
            $allErrors =  '';
            foreach ($validator->errors()->all() as $message){
                $allErrors =  $message;
                break;
            }

            $response	=	array(
                'status' 	=> 0,
                'message'	=> $allErrors,
                'detail'    => $detail
            );
        }else{

            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;


                $ceremony_id = $formData['ceremony_id'];
                $price = $formData['price'];
                $discount = $formData['discount'];
                $final_price = $formData['final_price'];
                $seats = $formData['seats'];
                $promocode_id = $formData['promocode_id'] ? $formData['promocode_id'] : 0;

                $ceremony_detail = Ceremony::where('id',$ceremony_id)->select('id','name','total_seats','remaining_seats','price','date')->first();
                $todayDate = date('Y-m-d');

                $userInfo = Customer::where('id',$user_id)->first();

                if($userInfo){
                    if($ceremony_detail){
                        if($todayDate > $ceremony_detail->date){
                            $status = 0;
                            $message = 'Ceremony Event has been passed';
                            $detail = $detail;
                        }elseif($seats > $ceremony_detail->remaining_seats){
                            $status = 0;
                            $message = 'Ceremony Event Seats has been filled.';
                            $detail = $detail;
                        }else{
                            $booking_no = 'booking' . $user_id . mt_rand(99999, 999999);
                            $obj 					=  new Bookings();
                            $obj->user_id			=  $user_id;
                            $obj->ceremony_id 		=  $ceremony_id;
                            $obj->booking_no 		=  $booking_no;
                            $obj->slug	 			=  '';
                            $obj->price				=  $price;
                            $obj->discount			=  $discount;
                            $obj->final_price		=  $final_price;
                            $obj->promocode_id		=  $promocode_id;
                            $obj->seats				=  $seats;
                            $obj->status			=  1;

                            $obj->save();
                            $bookingId	=	$obj->id;


                            $settingsEmail 	= Config::get('Site.email');
                            $full_name		= $userInfo->full_name;
                            $email			= $userInfo->email;

                            $emailActions	= EmailAction::where('action','=','booked_event_seats')->get()->toArray();
                            $emailTemplates	= EmailTemplate::where('action','=','booked_event_seats')->get(array('name','subject','action','body'))->toArray();

                            $cons 			= explode(',',$emailActions[0]['options']);
                            $constants 		= array();

                            foreach($cons as $key => $val){
                                $constants[] = '{'.$val.'}';
                            }

                            $subject 		= $emailTemplates[0]['subject'];
                            $rep_Array 		= array($full_name,$email,$ceremony_detail->name, $booking_no,$final_price,$seats,);
                            $messageBody	= str_replace($constants, $rep_Array, $emailTemplates[0]['body']);

//                            $mail			= $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);



                            $status = 1;
                            $message = 'Booking has been saved successfully.';
                            $detail = $detail;
                        }
                    }else{
                        $status = 0;
                        $message = 'Invalid Ceremony Event.';
                        $detail = $detail;
                    }
                }else{
                    $status = 0;
                    $message = 'Invalid User Detail.';
                    $detail = $detail;
                }

        }

        $response =	array(
            'status' 	=> $status,
            'message'	=> $message,
            'detail'   => $detail,
        );

        return Response::json($response); die;
    }

    public function checkSeatAvailability(Request $request){
        $eventid 	= $request->event_id;
        $seats   	= $request->seat;


        $ceremonies = Ceremony::with('booking')->where('id',$eventid)
            ->select('id','total_seats')->get();

        $response =array(
            'status'=> 1,
            'detail'=>$ceremonies,
        );
//        return Response::json($response);

        foreach ($ceremonies as $key => $value) {
            if(!empty($value->booking)&& !isNull($value->booking)){
                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                $rms = $value->total_seats - $sum;
                $value->remaining_seats = $rms < 0 ? 0 : $rms;
            }else{
                $value->remaining_seats = 0;
            }
        }

        if($seats > $value->remaining_seats){
            $response =	array(
                'status'=> 0,
                'message'=>'seats are not available.',
            );
            return Response::json($response);
        }else{
            $response =	array(
                'status'=> 1,
                'message'=>'seats are available.',
            );
            return Response::json($response);
        }

    }

    public function increaseSeat(Request $request){
        $bookingId 		=	$request->booking_id;
        $seat 			=	$request->no_of_seat;
        $amount 		=	$request->amount;
        $robeSize 		=	$request->robe_size;

        $response	= 	array();

        $messages   = array(
            'event_id.required' => "Please Enter Event ID",
            'no_of_seats.required'	=> "Please Enter No. of Seats",
            'amount.required' => "Please Enter Amount",
            'robe_size.required'=> "Please Enter Robe Size"
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'robe_size'	=> 'required',
                'no_of_seats'	=> 'required|min:1|integer',
                'amount' => 'required|numeric|between:0,99999.99'
            ), $messages
        );

        $user = Auth::guard('customers_api')->user();
        $userId = $user->id;

        $obj 						=  Booking::find($bookingId);
        $obj->no_of_seats			=  $seat;
        $obj->robe_size				=  $robeSize;
        $obj->save();

        $payment 				= new Payment();
        $payment->user_id 		= $userId;
        $payment->ceremony_id 	= $obj->event_id;
        $payment->booking_id 	= $obj->id;
        $payment->price 		= $amount;
        $payment->save();

        $response =array(
            'status'=> 1,
            'messages'=> 'Booking has been completed successfully.'
        );

        return  ($response);

    }

    public function eventList(Request $request){
        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.created_at';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'asc';

        $formData	= $request->all();
        $detail = (object) null;
        $response	= array();
        $messages = array(
            'user_id' 		    => "Please Enter User ID",
            'session_token' 	=> "Please Enter Session Token",
            'language_id' 	=> "Please Enter language_id",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'user_id' 			=> 'required',
                'session_token'     => 'required',
                'language_id'     => 'required',
            ), $messages
        );
        if ($validator->fails()){
            $allErrors =  '';
            foreach ($validator->errors()->all() as $message){
                $allErrors =  $message;
                break;
            }

            $response	=	array(
                'status' 	=> 0,
                'message'	=> $allErrors,
                'detail'    => $detail
            );
        }else{
            $user_id = $formData['user_id'];
            $session_token =  $formData['session_token'];
            $language_id =  $formData['language_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);

            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{
                $sessions_data  =  DB::table('sessions')->where('user_id',$user_id)->first();
                $userId 		=  $sessions_data->user_id;
                $userdetail  	=  DB::table('users')->select('gender')->where('id',$userId)->first();
                if(!empty($search)){
                    $ceremonies  = Ceremony::with('bookings')
                        ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        ->where('u.language_id',$language_id)
                        ->where(function ($query) use ($userdetail) {
                            $query->where('ceremony.ceremony_for', '=', 2)
                                ->orWhere('ceremony.ceremony_for', '=', $userdetail->gender);
                        })->where('u.name','like', '%' . $search . '%')
                        ->select('ceremony.id','u.name','u.description','total_seats','minimum_downpayment_amount','remaining_seats','price','ceremony_price','free_seats','date','status','u.address','u.latitude','u.longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(10);
                    foreach ($ceremonies as $key => $value) {
                        if(!empty($value->bookings)){
                            $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                            $value->no_of_seats = $sum;
                        }else{
                            $value->no_of_seats = 0;
                        }
                    }
                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );
                }else{
                    /*$ceremonies =  DB::select('SELECT * FROM ceremony LEFT JOIN ( SELECT  event_id ,sum(no_of_seats) as book_seat  FROM booking GROUP BY event_id ) b ON (ceremony.id=b.event_id)');
                    foreach ($ceremonies as $value) {
                        if (empty($value->event_id) || empty($value->book_seat)) {
                          //unset($value->event_id);
                          $value->event_id = "0";
                          $value->book_seat = "0";
                          }
                         $ceremoniess[] = $value;

                    }

                    if(!empty($ceremoniess)){

                        $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremoniess,
                    );
                    }else{
                        $response = array(
                        'status' 	=>  0,
                        'message'	=> "No Event or Booking",
                    );
                    }*/


                    $ceremonies  = Ceremony::with('bookings')
                        ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        ->where('u.language_id',$language_id)
                        ->where(function ($query) use ($userdetail) {
                            $query->where('ceremony.ceremony_for', '=', 2)
                                ->orWhere('ceremony.ceremony_for', '=', $userdetail->gender);
                        })->select('ceremony.id','u.name','u.description','total_seats','minimum_downpayment_amount','remaining_seats','price','ceremony_price','free_seats','date','status','u.address','u.latitude','u.longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(10);


                    foreach ($ceremonies as $key => $value) {
                        if(!empty($value->bookings)){
                            $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                            $value->no_of_seats = $sum;
                        }else{
                            $value->no_of_seats = 0;
                        }
                    }

                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );

                }
            }
        }

        return Response::json($response);
    }


    public function ceremonydetail(Request $request){

                $event_id = $request->event_id ;
                $ceremony = Ceremony::with('poll','poll.polloption')->where('id',$event_id)
                    ->get();
                $bookinglist = Booking::where('event_id',$event_id)
                    ->get();

                if(sizeof($bookinglist) >= $ceremony[0]->free_seats ){
                    $ceremony_status = 0;
                }
                else{
                    $ceremony_status = 1;
                }
        $user = Auth::guard('customers_api')->user();
        $user_id = $user->id;
                $ceremony[0]->ceremony_status = $ceremony_status;
                $ceremony[0]->id = (int)$ceremony[0]->id;
                $ceremony[0]->price = (int)$ceremony[0]->price;
                $ceremony[0]->status = (int)$ceremony[0]->status;
                $ceremony[0]->minimum_downpayment_amount = (double)$ceremony[0]->minimum_downpayment_amount;
                $ceremony[0]->ceremony_for = (int)$ceremony[0]->ceremony_for;
                $ceremony[0]->ceremony_price = (double)$ceremony[0]->ceremony_price;
                $ceremony[0]->free_seats = (int)$ceremony[0]->free_seats;
                $ceremony[0]->total_seats = (int)$ceremony[0]->total_seats;
                $ceremony[0]->poll = $ceremony[0]->poll;
                $ceremony[0]->number_of_booking_students = (int)sizeof($bookinglist);
                $ceremony[0]->booking = Booking::where('event_id',$event_id)->where('user_id',$user_id)->get();

                if(!empty($bookinglist)){
                    $sum = array_sum(array_column($bookinglist->toArray(), 'no_of_seats'));
                    $rms = $ceremony[0]->total_seats - $sum;
                    $ceremony[0]->remaining_seats = $rms < 0 ? 0 : $rms;
                }else{
                    $ceremony[0]->remaining_seats = 0;
                }
                $booking_data=$bookinglist;
                foreach($booking_data as $val)
                {
                    $val->id=(int)$val->id;
                    $val->user_id=(int)$val->user_id;
                    $val->event_id=(int)$val->event_id;
                    $val->no_of_seats=(int)$val->no_of_seats;
                    $val->amount=(int)$val->amount;
                    $val->remaining_amount=(int)$val->remaining_amount;
                }
                $response = array(
                    'status' 	=> 1,
                    'message'	=> "Success",
                    'ceremony' => $ceremony->all(),
                );
        return ($response);
    }





    /**
     * Function to make slug according model from any certain field
     *
     * @param title     as value of field
     * @param modelName as section model name
     * @param limit 	as limit of characters
     *
     * @return string
     */
    public function getSlug($title, $fieldName,$modelName,$limit = 30){
        $slug 		= 	 substr(Str::slug($title),0 ,$limit);
        $Model		=	"\App\Models\\$modelName";
        $slugCount 	=  count($Model::where($fieldName, 'regexp', "/^{$slug}(-[0-9]*)?$/i")->get());
        return ($slugCount > 0) ? $slug."-".$slugCount : $slug;
    }//end getSlug()

    /**
     * Function to make slug without model name from any certain field
     *
     * @param title     as value of field
     * @param tableName as table name
     * @param limit 	as limit of characters
     *
     * @return string
     */
    public function getSlugWithoutModel($title, $fieldName='' ,$tableName,$limit = 30){
        $slug 		=	substr(Str::slug($title),0 ,$limit);
        $slug 		=	Str::slug($title);
        $DB 		= 	DB::table($tableName);
        $slugCount 	= 	count( $DB->whereRaw("$fieldName REGEXP '^{$slug}(-[0-9]*)?$'")->get() );
        return ($slugCount > 0) ? $slug."-".$slugCount: $slug;
    }//end getSlugWithoutModel()
    /**
     * Function to send email form website
     *
     * @param string $to            as to address
     * @param string $fullName      as full name of receiver
     * @param string $subject       as subject
     * @param string $messageBody   as message body
     *
     * @return void
     */
    public function sendMail($to,$fullName,$subject,$messageBody, $from = '',$files = false,$path='',$attachmentName='') {
        $data				=	array();
        $data['to']			=	$to;
        $data['from']		=	(!empty($from) ? $from : Config::get("Site.email"));
        $data['fullName']	=	$fullName;
        $data['subject']	=	$subject;
        $data['filepath']	=	$path;
        $data['attachmentName']	=	$attachmentName;
        if($files===false){
            Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
                $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);

            });
        }else{
            if($attachmentName!=''){
                Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'],array('as'=>$data['attachmentName']));
                });
            }else{
                Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
                });
            }
        }

        DB::table('email_logs')->insert(
            array(
                'email_to'	 => $data['to'],
                'email_from' => $data['from'],
                'subject'	 => $data['subject'],
                'message'	 =>	$messageBody,
                'created_at' => DB::raw('NOW()')
            )
        );
    }//end sendMail()


    public function checkSeatAvailabilityForEvent($eventid,$seats){
        $ceremonies = Ceremony::with('booking')->where('id',$eventid)
            ->select('id','total_seats')->get();

        foreach ($ceremonies as $key => $value) {

            if(!empty($value->booking)){
                $sum = array_sum(array_column($value->booking->toArray(), 'no_of_seats'));

                $rms = $value->total_seats - $sum;

                $value->remaining_seats = $rms < 0 ? 0 : $rms;

            }else{
                $value->remaining_seats = 0;
            }
        }
        //	dd($value->remaining_seats);

        if($seats > $value->remaining_seats){
            return false;

        }else{
            return true;

        }

    }


}