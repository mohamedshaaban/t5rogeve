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

class EventsController extends Controller
{

    public function booking(Request $request){
        $formData	= $request->all();

        $messages = array(
            'event_id.required' => "Please Enter Event ID",
            'no_of_seats.required'	=> "Please Enter No. of Seats",
            'payment_type.required'	=> "Please Enter Payment Type",
            'amount.required' => "Please Enter Amount",
            'remaining_amount.required' => "Please Enter Remaining Amount",
            'trans_id.required' => "Please Enter Transation ID",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'no_of_seats'	=> 'required|min:1|integer',
                'payment_type'	=> 'required',
                'amount' => 'required',
                'remaining_amount' => 'required',
                'robe_size' => 'required_if:payment_type,full',
                'trans_id' => 'required',

            ), $messages
        );

        if ($validator->fails())
        {
            $allErrors =  '';
            foreach ($validator->errors()->all() as $message){
                $allErrors[] =  $message;
                break;
            }

            $response	=	array(
                'status' 	=> 0,
                'message'	=> $allErrors,
                'data'    => ''
            );

        }

        else{


            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
            $event_id = $formData['event_id'];
            $seats = $formData['no_of_seats'];
             $trans_id = $formData['trans_id'];


            $checkseat_detail = Ceremony::where('id',$event_id)->first();

            $isSeatAvailable = $this->checkSeatAvailabilityForEvent($event_id, $seats);
            //::where('id',$event_id)->select('remaining_seats')->first();
            if(!empty($checkseat_detail)){

                if(!$isSeatAvailable){
                    $response	=	array(
                        'status' 	=>  0,
                        'message'	=> 'Seats are not available.',
                        'data'    => ''
                    );
                    return Response::json($response);
                }


                $payment_details=PaymentLog::where('id',$trans_id)->first();

                if(!$payment_details||$payment_details->result!='CAPTURED')
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'data'    => $detail*/
                    );
                }
                else
                {
                    $event_id=$request->event_id;
                    $user_id=$user_id;

                    $no_of_seats=$request->no_of_seats;
                    $payment_type=$request->payment_type;
                    $amount=$request->amount;
                    $remaining_amount=$request->remaining_amount;
                    $robe_size=$request->robe_size;

                    $free_seats=$checkseat_detail->free_seats;
                    $seat_price=$checkseat_detail->price;
                    $ceremony_price=$checkseat_detail->ceremony_price;

                    $first_booking=Booking::select('ceremony_price')->where('user_id',$user_id)->where('event_id',$event_id)->first();

                    if($first_booking!=''){
                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'لا يمكن الحجز أكثر من مره',
                        );

                        return Response::json($response);
                    }

                    if($payment_type=='Full' || $payment_type=='full')
                    {
                        if($first_booking=='' && $no_of_seats==$free_seats)
                        {
                            $total_booking_seats= $no_of_seats;
                            $amount=$ceremony_price;
                        }
                        else if($first_booking=='' && $no_of_seats>$free_seats)
                        {
                            $total_booking_seats= $no_of_seats;

                        }

                        else if($first_booking=='' && $no_of_seats<$free_seats)
                        {

                            $response	=	array(
                                'status' 	=> 0,
                                'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                            );

                            return ($response);
                        }
                        else
                        {
                            $total_booking_seats= $no_of_seats;
                            $amount=$seat_price*$no_of_seats;
                        }


                    }
                    else if($payment_type=='Down' || $payment_type=='down')
                    {
                        if($first_booking=='' && $no_of_seats==$free_seats)
                        {
                            $total_booking_seats= $no_of_seats;
                        }
                        else if($first_booking=='' && $no_of_seats>$free_seats)
                        {
                            $total_booking_seats= $no_of_seats;
                        }
                        else if($first_booking=='' && $no_of_seats<$free_seats)
                        {

                            $response	=	array(
                                'status' 	=> 0,
                                'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                            );

                            return ($response);
                        }
                        else
                        {
                            $total_booking_seats= $no_of_seats;
                        }
                    }


                    $obj 					=  new Booking;
                    $obj->event_id			=  $event_id;
                    $obj->user_id			=  $user_id;
                    $obj->session_token		=  '';
                    $obj->no_of_seats		=  $total_booking_seats;
                    $obj->payment_type		=  $payment_type;
                    $obj->amount			=  $amount;
                    $obj->remaining_amount	=  $remaining_amount;
                    $obj->robe_size			=  $robe_size;
                    $obj->ceremony_price	=  $ceremony_price;
                    $obj->save();
                    $b_id	=	$obj->id;
                    $booking_seat= DB::table('booking')
                        ->select(DB::raw('SUM(no_of_seats) as no_of_seats'))
                        ->where('event_id',$event_id)
                        ->get();

                    $booking_val=
                        Ceremony::where('id', $event_id)
                        ->first();

                    $totalseat= $booking_val->total_seats;
                    $remainseat=$totalseat-$booking_seat[0]->no_of_seats;
                    Ceremony::where('id',$event_id)->update(['remaining_seats' => $remainseat]);

                    if(!empty($b_id))
                    {

                        $payment_arr = [
                            'user_id' => $obj->user_id,
                            'ceremony_id' => $obj->event_id,
                            'booking_id' => $b_id,
                            'price' => $obj->amount,
                            'payment_method'=>$obj->payment_type,
                            'status'=>'1',
                        ];

                        $pay_obj = new Payment();

                        foreach($payment_arr as $key => $value){
                            $pay_obj->$key = $value;
                        }
                        $pay_res = $pay_obj->save();
                        $response	=	array(
                            'status' 	=> 1,
                            'message'	=> 'Event Seat Book Successfully.',
                            'transationid'=>$payment_details->tranid,
                            'paymentID'=>$payment_details->paymentid,
                            'amount'=>$payment_details->amt,
                            'created_at'=>$payment_details->created_at
                        );
                    }
                }

            }
        }
        return ($response);
    }

    public function updatebooking(Request $request){

        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(
            'event_id.required' => "Please Enter Event ID",
            'user_id.required'  => "Please Enter User ID",
            'no_of_seats.required'	=> "Please Enter No. of Seats",
            'payment_type.required'	=> "Please Enter Payment Type",
            'amount.required' => "Please Enter Amount",
            'remaining_amount.required' => "Please Enter Remaining Amount",
            'trans_id.required' => "Please Enter Transation ID",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'user_id'  => 'required',
                'no_of_seats'	=> 'required|min:1|integer',
                'payment_type'	=> 'required',
                'amount' => 'required',
                'remaining_amount' => 'required',
                'robe_size' => 'required_if:payment_type,full',
                'trans_id' => 'required',

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
                'data'    => $detail
            );

        }

        else{


            $user_id = $formData['user_id'];
            $event_id = $formData['event_id'];
            $seats = $formData['no_of_seats'];
            $trans_id = $formData['trans_id'];



            $checkseat_detail = Ceremony::where('id',$event_id)->first();

            $isSeatAvailable = $this->checkSeatAvailabilityForEvent($event_id, $seats);
            //::where('id',$event_id)->select('remaining_seats')->first();
            if(!empty($checkseat_detail)){

                if(!$isSeatAvailable){
                    $response	=	array(
                        'status' 	=>  0,
                        'message'	=> 'Seats are not available.',
                        'data'    => $detail
                    );
                    return Response::json($response);
                }



                $event_id=$request->event_id;
                $user_id=$request->user_id;
                $no_of_seats=$request->no_of_seats;
                $payment_type=$request->payment_type;
                $amount=$request->amount;
                $remaining_amount=$request->remaining_amount;
                $robe_size=$request->robe_size;

                $free_seats=$checkseat_detail->free_seats;
                $seat_price=$checkseat_detail->price;
                $ceremony_price=$checkseat_detail->ceremony_price;


                if($payment_type=='Full' || $payment_type=='full')
                {

                    if($no_of_seats==$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;
                        $amount=$ceremony_price;
                    }
                    else if($no_of_seats>$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;
                        /*$new_seat=$no_of_seats-$free_seats;
                        $amount=$seat_price*$new_seat+$ceremony_price;*/
                    }

                    else if($no_of_seats<$free_seats)
                    {

                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                        );

                        return Response::json($response);
                    }
                    else
                    {
                        $total_booking_seats= $no_of_seats;
                        $amount=$seat_price*$no_of_seats;
                    }


                }
                else if($payment_type=='Down' || $payment_type=='down')
                {
                    if($no_of_seats==$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;

                    }
                    else if($no_of_seats>$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;

                    }

                    else if($no_of_seats<$free_seats)
                    {

                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                        );

                        return Response::json($response);
                    }
                    else
                    {

                        $total_booking_seats= $no_of_seats;
                    }

                }


                $onebooking = Booking::where('user_id',$user_id)
                    ->where('event_id',$event_id)
                    ->first();

                if(!empty($onebooking)){
                     $onebooking->no_of_seats		=  $total_booking_seats;
                    $onebooking->payment_type		=  $payment_type;
                    $onebooking->remaining_amount	=  $remaining_amount;
                    $onebooking->robe_size			=  $robe_size;
                    $onebooking->ceremony_price	=  $ceremony_price;
                    $onebooking->save();
                    $b_id	=	$onebooking->id;


                    ///

                    $booking_seat= DB::table('booking')
                        ->select(DB::raw('SUM(no_of_seats) as no_of_seats'))
                        ->where('event_id',$event_id)
                        ->get();

                    $booking_val= DB::table('ceremony')
                        ->select('*')
                        ->where('id', $event_id)
                        ->first();

                    $totalseat= $booking_val->total_seats;
                    $remainseat=$totalseat-$booking_seat[0]->no_of_seats;
                    DB::table('ceremony')->where('id',$event_id)->update(['remaining_seats' => $remainseat]);


                    if(!empty($b_id))
                    {

                        $payment_arr = [
                            'user_id' => $onebooking->user_id,
                            'ceremony_id' => $onebooking->event_id,
                            'booking_id' => $b_id,
                            'price' => $onebooking->amount,
                            'payment_method'=>$onebooking->payment_type,
                            'status'=>'1',
                        ];

                        $pay_obj = new Payment();

                        foreach($payment_arr as $key => $value){
                            $pay_obj->$key = $value;
                        }

                        $pay_res = $pay_obj->save();


                        $payment_details = PaymentLog::find($request->trans_id);
                        $response	=	array(
                            'status' 	=> 1,
                            'message'	=> 'Event Seat Book Successfully.',
                            'transationid'=>$payment_details->tranid,
                            'paymentID'=>$payment_details->paymentid,
                            'amount'=>$payment_details->amt,
                            'created_at'=>$payment_details->created_at
                            /*'data'    => $detail*/
                        );
                    }
                }

            }
        }

    }

    public function bookingfreeevent(Request $request){

        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(
            'event_id.required' => "Please Enter Event ID",
            'user_id.required'  => "Please Enter User ID",
            'session_token.required'	=> "Please Enter Session ID",
            'no_of_seats.required'	=> "Please Enter No. of Seats",
            'payment_type.required'	=> "Please Enter Payment Type",
            'amount.required' => "Please Enter Amount",
            'remaining_amount.required' => "Please Enter Remaining Amount",
            'trans_id.required' => "Please Enter Transation ID",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'user_id'  => 'required',
                'session_token'	=> 'required',
                'no_of_seats'	=> 'required|min:1|integer',
                'payment_type'	=> 'required',
                'amount' => 'required',
                'remaining_amount' => 'required',
                'robe_size' => 'required_if:payment_type,full',
                'trans_id' => 'required',

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
                'data'    => $detail
            );

        }

        else{


            $user_id = $formData['user_id'];
            $event_id = $formData['event_id'];
            $seats = $formData['no_of_seats'];
            $session_token =  $formData['session_token'];
            $trans_id = $formData['trans_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }

            $checkseat_detail = DB::table('ceremony')->select('*')->where('id',$event_id)->first();

            $isSeatAvailable = $this->checkSeatAvailabilityForEvent($event_id, $seats);
            //::where('id',$event_id)->select('remaining_seats')->first();
            if(!empty($checkseat_detail)){

                if(!$isSeatAvailable){
                    $response	=	array(
                        'status' 	=>  0,
                        'message'	=> 'Seats are not available.',
                        'data'    => $detail
                    );
                    return Response::json($response);
                }




                $event_id=$request->event_id;
                $user_id=$request->user_id;
                $session_token=$request->session_token;
                $no_of_seats=$request->no_of_seats;
                $payment_type=$request->payment_type;
                $amount=$request->amount;
                $remaining_amount=$request->remaining_amount;
                $robe_size=$request->robe_size;

                $free_seats=$checkseat_detail->free_seats;
                $seat_price=$checkseat_detail->price;
                $ceremony_price=$checkseat_detail->ceremony_price;

                $first_booking=Book::select('ceremony_price')->where('user_id',$user_id)->where('event_id',$event_id)->first();

                if($first_booking!=''){
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'لا يمكن الحجز أكثر من مره',
                    );

                    return Response::json($response);
                }

                if($payment_type=='Full' || $payment_type=='full')
                {


                    //first time event booking price

                    /*if($first_booking=='')
                    {
                        $amount=$seat_price*$no_of_seats+$ceremony_price;
                        $total_booking_seats= $no_of_seats+$free_seats;
                    }
                    else
                    {
                        $amount=$seat_price*$no_of_seats;

                        $total_booking_seats= $no_of_seats;
                    }*/

                    if($first_booking=='' && $no_of_seats==$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;
                        $amount=$ceremony_price;
                    }
                    else if($first_booking=='' && $no_of_seats>$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;
                        /*$new_seat=$no_of_seats-$free_seats;
                        $amount=$seat_price*$new_seat+$ceremony_price;*/
                    }

                    else if($first_booking=='' && $no_of_seats<$free_seats)
                    {

                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                        );

                        return Response::json($response);
                    }
                    else
                    {
                        $total_booking_seats= $no_of_seats;
                        $amount=$seat_price*$no_of_seats;
                    }


                }
                else if($payment_type=='Down' || $payment_type=='down')
                {
                    //first time event booking price

                    /*if($first_booking=='')
                    {
                        $total_booking_seats=$no_of_seats+$free_seats;

                    }
                    else
                    {

                        $total_booking_seats=$no_of_seats;

                    }*/

                    if($first_booking=='' && $no_of_seats==$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;

                    }
                    else if($first_booking=='' && $no_of_seats>$free_seats)
                    {
                        $total_booking_seats= $no_of_seats;

                    }

                    else if($first_booking=='' && $no_of_seats<$free_seats)
                    {

                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'You need to book minimum '.$free_seats.' seats.',
                        );

                        return Response::json($response);
                    }
                    else
                    {

                        $total_booking_seats= $no_of_seats;
                        //$amount=$seat_price*$no_of_seats;
                    }

                }


                $obj 					=  new Booking;
                $obj->event_id			=  $event_id;
                $obj->user_id			=  $user_id;
                $obj->session_token		=  $session_token;
                $obj->no_of_seats		=  $total_booking_seats;
                $obj->payment_type		=  $payment_type;
                $obj->amount			=  $amount;
                $obj->remaining_amount	=  $remaining_amount;
                $obj->robe_size			=  $robe_size;
                $obj->ceremony_price	=  $ceremony_price;
                /*$obj->is_verified       = 1;*/
                $obj->save();
                $b_id	=	$obj->id;



                $booking_seat= DB::table('booking')
                    ->select(DB::raw('SUM(no_of_seats) as no_of_seats'))
                    ->where('event_id',$event_id)
                    ->get();

                $booking_val= DB::table('ceremony')
                    ->select('*')
                    ->where('id', $event_id)
                    ->first();

                $totalseat= $booking_val->total_seats;
                $remainseat=$totalseat-$booking_seat[0]->no_of_seats;
                DB::table('ceremony')->where('id',$event_id)->update(['remaining_seats' => $remainseat]);

                date_default_timezone_set('Asia/Kuwait');
                $date = date('m/d/Y h:i:s a', time());
                $response	=	array(
                    'status' 	=> 1,
                    'message'	=> 'Event Seat Book Successfully.',
                    'amount' => $amount,
                    'created_at'=> $date
                );


            }
        }
        return Response::json($response);
    }


    public function bookingEdit(Request $request){


        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(
            'booking_id.required' => "Please Enter Booking ID",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'booking_id' => 'required',

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
                'data'    => $detail
            );

        }

        else{


            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
            $booking_id = $formData['booking_id'];
            $seats = $formData['no_of_seats'];
             $trans_id 	= $formData['trans_id'];
            $robe_size 	= $formData['rob_size'];




            $checkseat_detail = DB::table('booking')->find($booking_id);

            $isSeatAvailable = $this->checkSeatAvailabilityForEvent($checkseat_detail->event_id, $seats);
            //::where('id',$event_id)->select('remaining_seats')->first();
            if(!empty($checkseat_detail)){

                if(!$isSeatAvailable){
                    $response	=	array(
                        'status' 	=>  0,
                        'message'	=> 'Seats are not available.',
                        'data'    => $detail
                    );
                    return Response::json($response);
                }

                if(empty($trans_id))
                {
                    Booking::where('id',$booking_id)->update(['robe_size' => $robe_size]);

                    $response	=	array(
                        'status' 	=> 1,
                        'message'	=> "Booking Updated Successfully",
                        /*'data'   => $data,*/

                    );
                    return Response::json($response);


                }
                $booking_details=Booking::where('id',$booking_id)->first();
                $booking_user=$booking_details->user_id;
                $ceremony_id=$booking_details->event_id;
                $prive_amount=$booking_details->amount;
                $pri_no_of_seats=$booking_details->no_of_seats;

                $ceremony_details=Ceremony::where('id',$ceremony_id)->first();
                $price=$ceremony_details->price;
                $allseats=$ceremony_details->total_seats;
                $new_amout=$price*$seats;

                $total_seats=$pri_no_of_seats+$seats;
                $total_price=$prive_amount+$new_amout;

                $payment_details=PaymentLog::where('id',$trans_id)->first();

                if($payment_details->result!='CAPTURED' || $payment_details->amt!=$new_amout)
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'data'    => $detail*/
                    );
                }
                else
                {
                    if(empty($robe_size))
                    {
                        $results=Booking::where('id',$booking_id)->update(['no_of_seats' => $total_seats,'amount'=>$total_price,'full_name'=>$request->full_name,'father_name'=>$request->father_name,'grandfather_name'=>$request->grandfather_name,'family_name'=>$request->family_name]);
                    }
                    else
                    {
                        $results=Booking::where('id',$booking_id)->update(['no_of_seats' => $total_seats,'amount'=>$total_price,'robe_size'=>$robe_size,'full_name'=>$request->full_name,'father_name'=>$request->father_name,'grandfather_name'=>$request->grandfather_name,'family_name'=>$request->family_name]);
                    }


                    //update remaining seats
                    $booking_seat= DB::table('booking')
                        ->select(DB::raw('SUM(no_of_seats) as no_of_seats'))
                        ->where('event_id',$ceremony_id)
                        ->get();


                    $remainseat=$allseats-$booking_seat[0]->no_of_seats;
                    DB::table('ceremony')->where('id',$ceremony_id)->update(['remaining_seats' => $remainseat]);

                    //end remaining seats

                    $response	=	array(
                        'status' 	=> 1,
                        'message'	=> "Booking Updated Successfully",
                        'transationid'=>$payment_details->tranid,
                        'paymentID'=>$payment_details->paymentid,
                        'amount'=>$payment_details->amt,
                        'created_at'=>$payment_details->created_at
                        /*'data'   => $data,*/

                    );

                }
            }
        }

        return Response::json($response);

    }


    public function bookingDelete($id,Request $request)
    {

        $formData=$request->all();
        $detail = (object) null;

        $response=array();
        $messages=array(
            'user_id.required'			=> 'Please Enter UserID',
            'session_token.required'	=> 'Please Enter Session ID',
            'booking_id.required'		=> 'Please Enter Booking ID'
        );

        $validator=validator::make(
            $request->all(),
            array(
                'user_id'		=> 'required',
                'session_token'	=> 'required',
                'booking_id'	=>	'required'
            ),$messages
        );

        if($validator->fails())
        {

            $allErrors ='';
            foreach ($validator->errors()->all() as $message) {
                $allErrors=$message;
                break;
            }

            $response=array('status'=>0,'message'=>$allErrors,'data'=> $detail);
        }
        else
        {
            $user_id = $formData['user_id'];
            $booking_id = $formData['booking_id'];
            $session_token =  $formData['session_token'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }


            $booking =	Booking::find($booking_id);
            if(!empty($booking))
            {
                $booking->delete();
                $response	=	array(
                    'status' 	=> 1,
                    'message'	=> "Booking Deleted Successfully",
                    /*'data'   => $data,*/

                );
            }
            else
            {
                $response	=	array(
                    'status' 	=> 0,
                    'message'	=> 'Unsuccessful',
                    /*'data'	=> $detail*/
                );
            }

        }

        return Response::json($response);

    }
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