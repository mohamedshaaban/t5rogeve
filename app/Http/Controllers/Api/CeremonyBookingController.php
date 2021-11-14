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

class CeremonyBookingController extends Controller
{

    public function booking_csvfile(Request $request)
    {

        $formData=$request->all();
        $detail = (object) null;

        $response=array();
        $messages=array(
            'booking_id.required'		=> 'Please Enter Booking ID'
        );

        $validator=validator::make(
            $request->all(),
            array(

                'booking_id'	=>	'required'
            ),$messages
        );

        if($validator->fails())
        {

            $allErrors ='';
            foreach ($validator->errors()->all() as $message) {
                $allErrors[]=$message;
                break;
            }

            $response=array('status'=>0,'message'=>$allErrors,'data'=> $detail);

            return ($response);
        }
        else
        {

            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
            $booking_id = $formData['booking_id'];



            $results			=	Booking::where('id', '=', $booking_id)->first();

            if(empty($results))
            {
                $response =array(
                    'status'=> 1,
                    'messages'=> 'Fail.'
                );
            }
            else
            {
                $user = Customer::where('id',$results->user_id)->first();
                $items = Ceremony::where('id',$results->event_id)->first();

                $headers = array(
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=bookfile.csv",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                );

                $columns = array('Booking ID','Event Name', 'User Name', 'Seats Book', 'Payment Type', 'Amount', 'Remaining Amount','Robe Size');

                $result=array($booking_id, $items->name, $user->full_name, $results->no_of_seats, $results->payment_type, $results->amount, $results->remaining_amount,$results->robe_size);

                $callback = function() use ($result, $columns)
                {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);


                    fputcsv($file,$result);

                    fclose($file);
                };

                return Response::stream($callback, 200, $headers);
            }
        }

    }

    public function booking_allcsvfile(Request $request)
    {

        $formData=$request->all();
        $detail = (object) null;

        $response=array();
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=bookfile.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );


            $results			=	Booking::all();

            $columns = array('Booking ID','Event Name', 'User Name', 'Seats Book', 'Payment Type', 'Amount', 'Remaining Amount','Robe Size');

            $callback = function() use ($results, $columns)
            {
                $file = fopen('php://output', 'w');
                /*fputcsv($file, $columns);
                fputcsv($file,$data);*/
                fputcsv($file, $columns);
                foreach($results as $value) {
                    $user = Customer::where('id',$value->user_id)->first();
                    $items = Ceremony::where('id',$value->event_id)->first();
                    if($items)
                    {
                        fputcsv($file, array($value->id, $items->name, $user->full_name, $value->no_of_seats, $value->payment_type, $value->amount, $value->remaining_amount,$value->robe_size));
                    }
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);



    }

    public function bookingList(Request $request)
    {
        $search = $request->name;
        $sort_by = $request->sort_by ? $request->sort_by : 'booking.id';
        $sort_type = $request->sort_type ? $request->sort_type : 'desc';
        $formData = $request->all();
        $detail = (object)null;



            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;

                $booking = Booking::with('ceremony','ceremony.amenities','ceremony.poll.polloption.pollanswered', 'ceremonyWithDescription', 'payments')
                    ->whereHas('ceremony', function($q){
                        $q->where('date', '>=', \Carbon\Carbon::today()->format('Y-m-d'));
                    })->where('user_id', $user_id)->orderBy('id', 'desc')->paginate()->toArray();

                $booking['next_page_url'] = (!empty($booking['next_page_url'])) ? str_replace('/?', '?', $booking['next_page_url']) : '';
                $booking['prev_page_url'] = (!empty($booking['prev_page_url'])) ? str_replace('/?', '?', $booking['prev_page_url']) : '';


                $total = $booking['total'];
                $per_page = $booking['per_page'];
                $current_page = $booking['current_page'];
                $last_page = $booking['last_page'];
                $next_page_url = $booking['next_page_url'];
                $prev_page_url = $booking['prev_page_url'];
                $from = $booking['from'];
                $to = $booking['to'];
                $data_details = array();
                $data = $booking['data'];

                foreach ($data as $value) {
                    if(!isset($value['ceremony']))
                    {
                        continue;
                    }
                     $id = (int)$value['id'];
                    $user_id = (int)$value['user_id'];
                    $event_id = (int)$value['event_id'];
                    $event = Ceremony::find($event_id);

                    $paymentLogAmt = PaymentLog::where(
                        'user_id',$user_id)->where(
                        'event_id',$event_id)->where('result','CAPTURED')->sum('amt');

                    $remainingamount =($event->ceremony_price-$paymentLogAmt);


                    $no_of_seats = (int)$value['no_of_seats'];
                    $amount = (int)$value['amount'];
                    $ceremony_price = $value['ceremony_price'];
                    $payment_type = $value['payment_type'];
                    $remaining_amount = (double)$value['remaining_amount'];
                    $robe_size = $value['robe_size'];
                    $session_token = $value['session_token'];
                    $enroll_amt = $value['enroll_amt'];
                    $created_at = $value['created_at'];
                    $updated_at = $value['updated_at'];
                    $imageterm = $value['ceremony']['imageterm'];
                    $imageterm2 = $value['ceremony']['imageterm2'];
                    $hide_seats = (int)$value['ceremony']['hide_seats'];
                    $NameExDate = $value['ceremony']['Name_Ex_Date'];
                    $RobeExDate = $value['ceremony']['RobSize_Ex_Date'];
                    $ceremony = $value['ceremony'];
                    $poll = $value['ceremony']['poll'];
                    $amenities = $value['ceremony']['amenities'];

                    $ceremony_with_description = $value['ceremony'];
                    if (empty($ceremony_with_description)) {
                        $ceremony_data = new \stdClass();
                    } else {
                        $ceremony_id = (int)$ceremony_with_description['id'];
                        $ceremony_name = $ceremony['name'];
                        $ceremony_description = $ceremony_with_description['description'];
                        $ceremony_total_seats = (int)$ceremony_with_description['total_seats'];
                        $ceremony_remaining_seats = (int)$ceremony['remaining_seats'];
                        $ceremony_price = (int)$ceremony_with_description['price'];
                        $ceremony_ceremony_price = (double)$ceremony_with_description['ceremony_price'];
                        $ceremony_free_seats = (int)$ceremony_with_description['free_seats'];
                        $ceremony_status = (int)$ceremony_with_description['status'];
                        $ceremony_faculty = $ceremony_with_description['faculty'];
                        $ceremony_minimum_downpayment_amount = (double)$ceremony_with_description['minimum_downpayment_amount'];

                        $ceremony_created_at = $ceremony_with_description['created_at'];
                        $ceremony_updated_at = $ceremony_with_description['updated_at'];
                        $ceremony_date = $ceremony_with_description['date'];
                        $ceremony_address = $ceremony_with_description['address'];
                        $ceremony_latitude = $ceremony_with_description['latitude'];

                        $ceremony_longitude = $ceremony_with_description['longitude'];
                        $ceremony_for = (int)$ceremony_with_description['ceremony_for'];
                        $ceremony_image = $ceremony['imagemain'];
                        $ceremony_medium_image = $ceremony['imagemain'];
                        $ceremony_thumbnail_image = $ceremony['imagemain'];

                        $ceremony_data = array(
                            "id" => $ceremony_id,
                            "name" => $ceremony_name,
                            "description" => $ceremony_description,
                            "total_seats" => $ceremony_total_seats,
                            "remaining_seats" => $ceremony_remaining_seats,
                            "ceremony_price" => $ceremony_price,
                            "free_seats" => $ceremony_free_seats,
                            "imagemain" => $ceremony_image,
                            "status" => $ceremony_status,
                            "hide_seats" => $hide_seats,
                            "Name_Ex_Date" => $NameExDate,
                            "Robe_Ex_Date" => $RobeExDate,
                            "NameExDate" => $NameExDate,
                            "RobeExDate" => $RobeExDate,
                            "imageterm" => $imageterm,
                            "imageterm2" => $imageterm2,
                            "faculty" => $ceremony_faculty,
                            "minimum_downpayment_amount" => $ceremony_minimum_downpayment_amount,
                            "created_at" => $ceremony_created_at,
                            "updated_at" => $ceremony_updated_at,
                            "date" => $ceremony_date,
                            "address" => $ceremony_address,
                            "latitude" => $ceremony_latitude,
                            "longitude" => $ceremony_longitude,
                            "ceremony_for" => $ceremony_for,
                            "ceremony_image" => $ceremony_image,
                            "ceremony_medium_image" => $ceremony_medium_image,
                            "ceremony_thumbnail_image" => $ceremony_thumbnail_image,
                            "poll" => $poll,
                            "amenities" => $amenities

                        );
                    }

                    $payments = $value['payments'];

                    $payment_data = array();
                    foreach ($payments as $val) {
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

                        array_push($payment_data, array("id" => $id, "user_id" => $payment_user_id, "ceremony_id" => $payment_ceremony_id, "booking_id" => $payment_booking_id, "price" => $payment_price, "transaction_no" => $payment_transaction_no, "payment_method" => $payment_payment_method, "status" => $payment_status, "created_at" => $payment_created_at, "updated_at" => $payment_updated_at));
                    }


                    array_push($data_details, array(
                        "id" => $id,
                        "user_id" => $user_id,
                        "event_id" => $event_id,
                        "no_of_seats" => $no_of_seats,
                        "amount" => $amount,
                        "Name_Ex_Date" => $NameExDate,
                        "Robe_Ex_Date" => $RobeExDate,
                        "NameExDate" => $NameExDate,
                        "RobeExDate" => $RobeExDate,
                        "imageterm" => $imageterm,
                        "ceremony_price" => $ceremony_price,
                        "payment_type" => $payment_type,
                        "remaining_amount" => $remaining_amount,
                        "robe_size" => $robe_size,
                        "session_token" => $session_token,
                        "enroll_amt" => $enroll_amt,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "ceremony_with_description" => $ceremony_data,
                        "payments" => $payment_data));

                }

                $booking_responce = array("total" => $total, "per_page" => $per_page, "current_page" => $current_page, "last_page" => $last_page, "next_page_url" => $next_page_url, "prev_page_url" => $prev_page_url, "from" => $from, "to" => $to, "data" => $data_details);


                if (empty($booking)) {
                    $response = array(
                        'status' => 1,
                        'messages' => 'Booking List',
                        'data' => $booking_responce,
                    );
                } else {
                    $response = array(
                        'status' => 1,
                        'messages' => 'Booking List',
                        'data' => $booking_responce,
                    );
                }




            return ($response);
        }


    public function onebookingList(Request $request){
        $eventid 	= $request->event_id;
        $user_id   	= $request->user_id;
        $response	= array();

        $onebooking = Booking::with("ceremony","ceremony_with_description")->where('user_id',$user_id)
            ->where('event_id',$eventid)
            ->get();





        if(empty($onebooking)){
            $response=array(
                'status'=>0,
                'message'=> 'قائمة الحجوزات',
                'data'=> $onebooking);
        }else{
            $response=array(
                'status'=>1,
                'message'=> 'قائمة الحجوزات',
                'data'=> $onebooking);
        }

        return ($response);


    }

    public function changerobesize(Request $request){


        $detail = (object) null;

        $response	= array();
        $messages = array(
            'event_id.required' => "Please Enter Event ID",

            'robe_size.required'	=> "Please Select Robe_size",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'robe_size' => 'required',

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

        }else{

            $event_id=$request->event_id;
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
            $robe_size=$request->robe_size;

            Booking::
                where('user_id',$user_id)
                ->where('event_id',$event_id)
                ->update(['robe_size' => $robe_size]);

            $response	=	array(
                'status' 	=>  1,
                'message'	=> 'تم تعديل مقاس الروب'
            );
            return ($response);

        }
        return ($response);

    }

    public function addBookingPayment(Request $request){

        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(

            'bookingid.required' => "Please Enter Booking ID",
            'trans_id.required' => "Please Enter Trans Id",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'bookingid' => 'required',
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
            $user = Auth::guard('customers_api')->user();
            $user_id = $user->id;
            $bookingid = $formData['bookingid'];

            $trans_id = $formData['trans_id'];


            $booking = Booking::select('*')->where('id',$bookingid)->first();


            if(!empty($booking->remaining_amount)){

                $payment_details=DB::table('payment_log')->select('*')->where('tranid',$trans_id)->first();

                if(!$payment_details || $payment_details->result!='CAPTURED')
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'data'    => $detail*/
                    );
                }
                else
                {

                    $user_id=$request->user_id;
                    $session_token=$request->session_token;
                    $amount=$booking->amount;
                    $remaining_amount=$booking->remaining_amount;
                    $total_amount=$amount+$remaining_amount;
                    Booking::where('id',$bookingid)->update(['amount' => $total_amount,'remaining_amount' =>'0','payment_type'=>'Full']);

                    DB::table('payments')
                        ->where('booking_id',$bookingid)
                        ->where('status','1')
                        ->update(['price' => $total_amount,'status' =>'1','payment_method'=>'Full']);

                    $response	=	array(
                        'status' 	=> 1,
                        'message'	=> 'تم حجز مقعد ألفاعلية بنجاح.',
                        'transationid'=>$payment_details->tranid,
                        'paymentID'=>$payment_details->paymentid,
                        'amount'=>$payment_details->amt,
                        'created_at'=>$payment_details->created_at
                        /*'data'    => $detail*/
                    );
        $booking = Booking::where('id',$bookingid)->first();
        $event =  Ceremony::where('id',$booking->event_id)
            ->first();
            if($event)
            {
        $totalseats = Booking::where('event_id',$booking->event_id)->sum('no_of_seats');
     
            $total_amount=$event->ceremony_price + ($booking->no_of_seats-$event->free_seats)*$event->price;

        if($booking->downpayment_amount1 != $event->minimum_downpayment_amount){
            $booking->downpayment_amount1 = $event->minimum_downpayment_amount;
        }
        if($booking->downpayment_amount2 != $event->downpayment_amount2){
            $booking->downpayment_amount2 = $event->downpayment_amount2;
        }
        if($booking->downpayment_amount3 != $event->downpayment_amount3){
            $booking->downpayment_amount3 = $event->downpayment_amount3;
        }
        if($booking->ceremony_price != $event->ceremony_price){
            $booking->ceremony_price = $event->ceremony_price;
        }
        
        $booking->total_amount = $total_amount ;
        $paymentLogAmt = PaymentLog::where(
            'user_id',$booking->user_id)->where(
            'event_id',$booking->event_id)->where('result','CAPTURED')->sum('amt');
        
        $booking->remaining_amount=   $event->ceremony_price-$paymentLogAmt;
        $booking->save();
        }
                }

            }
            else
            {
                $response	=	array(
                    'status' 	=>  0,
                    'message'	=> 'لا يمكن أن يكون المبلغ المتبقي أكبر من 0.',

                );
                return  ($response);

            }
        }
        return  ($response);
    }

}