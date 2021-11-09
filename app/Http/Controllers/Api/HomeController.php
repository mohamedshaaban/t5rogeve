<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Sponsorplatinum;
use Backpack\NewsCRUD\app\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class HomeController extends Controller
{
    public function NotificationsList(Request $request)
    {

        $formData=$request->all();
        $sort_by     	= $request->sort_by ? $request->sort_by : 'created_at';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

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

            $user_event_details=Booking::where('user_id',$user_id)->select('event_id')->get();

            $eventid = [];
            foreach($user_event_details as $key => $value) {

                $eventid[] = $user_event_details[$key]->event_id;

            }
            //	return ($eventid);

            //	$result=Notifications::whereIn('eventid',$eventid)->get();
            /*	$result= Notifications::where(function($query) use ($user_id) {
                     $query->where('userid', '=', $user_id)
                    ->orWhere('alluser', '=', 1);
                    //->orWhereIn('eventid',$eventid);
                })*/
            $result= Notification::where('userid',  $user_id)
                ->orWhere('alluser', '=', 1)
                ->orWhereIn('eventid',$eventid)
                ->orderBy($sort_by, $sort_type)->get();

            //$result=Notifications::all();

            $response=array('status'=>1,'data'=>$result);
            return Response::json($response);

        }
    }
    public function GetTransactionByUserId(Request $request)
    {
        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'booking.id';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

        $formData	= $request->all();
        $detail = (object) null;



        $language_id =  isset($request->language_id) ? $request->language_id : 1;



        \Session::put("lang",$language_id);

        $user_id=Auth::guard('customers_api')->user()->id;
            $p_no   =  $request->page - 1;
            $total  =  $request->limit * $p_no;

            $transaction_detail= DB::table('payment_log as pl')
                ->join('customers as u', 'pl.user_id', '=', 'u.id')
                ->Leftjoin('ceremony as c', 'c.id', '=', 'pl.event_id')
                ->where('pl.user_id',Auth::guard('customers_api')->user()->id)
                ->select('pl.*','u.full_name','c.name as event_name')
                ->orderBy('pl.id', 'desc')
                ->get();


            if($transaction_detail!=''){
                $errors	= 'Data successfully fetch';
                $response	=	array(
                    'status' 	=>1,
                    'message'	=> $errors,
                    'data'     =>  $transaction_detail
                );

            }else{
                $response	=	array(
                    'status' 	=>0,
                    'message'	=> 'data not found',
                    'data'     =>  ''
                );
            }



        return  ($response);

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

        $result=Sponsorplatinum::where('status',1)->get();
        //	$result=SponsorPlatinum::all;
        $result= Sponsorplatinum::orderBy($sort_by, $sort_type)->get();

        $response=array('status'=>1,'data'=>$result);
        return ($response);

        //	}
    }
    public function bookingList(Request $request){
        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'booking.id';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

        $formData	= $request->all();
        $detail = (object) null;


        $user_id=Auth::guard('customers_api')->user()->id;
        dd($user_id);
                $booking = Booking::with('ceremony' , 'ceremonyWithDescription','payments')
                    ->where('user_id',$user_id)->orderBy('id','desc')->paginate(15);
                 //	return Response::json($booking);

                 $booking['next_page_url'] = (!empty($booking['next_page_url']))?str_replace('/?','?',$booking['next_page_url']):'';
                $booking['prev_page_url'] = (!empty($booking['prev_page_url']))?str_replace('/?','?',$booking['prev_page_url']):'';

                $total= $booking['total'];
                $per_page= $booking['per_page'];
                $current_page= $booking['current_page'];
                $last_page= $booking['last_page'];
                $next_page_url= $booking['next_page_url'];
                $prev_page_url= $booking['prev_page_url'];
                $from= $booking['from'];
                $to= $booking['to'];
                $data_details=array();
                $data= $booking ;

                foreach($data as $value)
                {
                    if(!$value)
                    {
                        continue;
                    }
                       $id = (int)$value->id;
                    $user_id = (int)$value['user_id'];

                    $event_id = (int)$value['event_id'];
                    $no_of_seats = (int)@value['no_of_seats'];
                    $amount = (int)$value['amount'];
                    $ceremony_price = $value['ceremony_price'];
                    $payment_type = $value['payment_type'];
                    $remaining_amount = (double)$value['remaining_amount'];
                    $robe_size = $value['robe_size'];
                    $enroll_amt = $value['enroll_amt'];
                    $created_at = $value['created_at'];
                    $updated_at = $value['updated_at'];
                    $imageterm = $value['ceremony']['imageterm'];
                    $hide_seats= (int)$value['ceremony']['hide_seats'];
                    $NameExDate = $value['ceremony']['Name_Ex_Date'];
                    $RobeExDate = $value['ceremony']['RobSize_Ex_Date'];
                    $ceremony =  $value['ceremony'];

                    $ceremony_with_description = $value['ceremony'];
                    if(empty($ceremony_with_description))
                    {
                        $ceremony_data=  new \stdClass();
                    }
                    else
                    {
                        $ceremony_id= (int)$ceremony_with_description['id'];
                        $ceremony_name= $ceremony['name'];
                        $ceremony_description =$ceremony_with_description['description'];
                        $ceremony_total_seats= (int)$ceremony_with_description['total_seats'];
                        $ceremony_remaining_seats= (int)$ceremony['remaining_seats'];
                        $ceremony_price= (int)$ceremony_with_description['price'];
                        $ceremony_ceremony_price= (double)$ceremony_with_description['ceremony_price'];
                        $ceremony_free_seats= (int)$ceremony_with_description['free_seats'];
                        $ceremony_status= (int)$ceremony_with_description['status'];
                        $ceremony_faculty= $ceremony_with_description['faculty'];
                        $ceremony_minimum_downpayment_amount= (double)$ceremony_with_description['minimum_downpayment_amount'];

                        $ceremony_created_at= $ceremony_with_description['created_at'];
                        $ceremony_updated_at= $ceremony_with_description['updated_at'];
                        $ceremony_date= $ceremony_with_description['date'];
                        $ceremony_address= $ceremony_with_description['address'];
                        $ceremony_latitude= $ceremony_with_description['latitude'];

                        $ceremony_longitude= $ceremony_with_description['longitude'];
                        $ceremony_for= (int)$ceremony_with_description['ceremony_for'];
                        $ceremony_image= $ceremony['imagemain'];
                        $ceremony_medium_image= $ceremony['imagemain'];
                        $ceremony_thumbnail_image= $ceremony['imagemain'];

                        $ceremony_data=array(
                            "id"=>$ceremony_id,
                            "name"=>$ceremony_name,
                            "description"=>$ceremony_description,
                            "total_seats"=>$ceremony_total_seats,
                            "remaining_seats"=>$ceremony_remaining_seats,
                            "ceremony_price"=>$ceremony_price,
                            "free_seats"=>$ceremony_free_seats,
                            "imagemain"=>$ceremony_image,
                            "status"=>$ceremony_status,
                            "hide_seats"=>$hide_seats,
                            "NameExDate"=>$NameExDate,
                            "RobeExDate"=>$RobeExDate,
                            "imageterm"=>$imageterm,
                            "faculty"=>$ceremony_faculty,
                            "minimum_downpayment_amount"=>$ceremony_minimum_downpayment_amount,
                            "created_at"=>$ceremony_created_at,
                            "updated_at"=>$ceremony_updated_at,
                            "date"=>$ceremony_date,
                            "address"=>$ceremony_address,
                            "latitude"=>$ceremony_latitude,
                            "longitude"=>$ceremony_longitude,
                            "ceremony_for"=>$ceremony_for,
                            "ceremony_image"=>$ceremony_image,
                            "ceremony_medium_image"=>$ceremony_medium_image,
                            "ceremony_thumbnail_image"=>$ceremony_thumbnail_image

                        );
                    }



                    $payments = $value['payments'];

                    $payment_data=array();
                    foreach($payments as $val)
                    {
                        $payment_id = (int)$val['id'];
                        $payment_user_id = (int)$val['user_id'];
                        $payment_ceremony_id = (int)$val['ceremony_id'];
                        $payment_booking_id = (int)$val['booking_id'];
                        $payment_price = (double)$val['price'];
                        $payment_transaction_no = $val['transaction_no'];
                        $payment_payment_method = $val['payment_method'];
                        $payment_status = (int)$val['status'];
                        $payment_created_at = $val['created_at'];
                        $payment_updated_at = $val['updated_at'];

                        array_push($payment_data,array("id"=>$id,"user_id"=>$payment_user_id,"ceremony_id"=>$payment_ceremony_id,"booking_id"=>$payment_booking_id,"price"=>$payment_price,"transaction_no"=>$payment_transaction_no,"payment_method"=>$payment_payment_method,"status"=>$payment_status,"created_at"=>$payment_created_at,"updated_at"=>$payment_updated_at));
                    }


                    array_push($data_details,array(
                        "id"=>$id,
                        "user_id"=>$user_id,
                        "event_id"=>$event_id,
                        "no_of_seats"=>$no_of_seats,
                        "amount"=>$amount,
                        "NameExDate"=>$NameExDate,
                        "RobeExDate"=>$RobeExDate,
                        "imageterm"=>$imageterm,
                        "ceremony_price"=>$ceremony_price,
                        "payment_type"=>$payment_type,
                        "remaining_amount"=>$remaining_amount,
                        "robe_size"=>$robe_size,
                        "enroll_amt"=>$enroll_amt,
                        "created_at"=>$created_at,
                        "updated_at"=>$updated_at,
                        "ceremony_with_description"=>$ceremony_data,
                        "payments"=>$payment_data));

                }

                $booking_responce= array("total"=>$total,"per_page"=>$per_page,"current_page"=>$current_page,"last_page"=>$last_page,"next_page_url"=>$next_page_url,"prev_page_url"=>$prev_page_url,"from"=>$from,"to"=>$to,"data"=>$data_details);


                    $response =array(
                        'status'=> 1,
                        'messages'=> 'Booking List',
                        'detail'=>$booking_responce,
                    );





        return  ($response);
        //return Response::json($response, 200, [], JSON_NUMERIC_CHECK);
    }

}