<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\User;
use App\Models\Ceremony;
use App\Models\Booking;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth,Response,DB,Hash,Session,Redirect,Validator;

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

                    $ceremonies = Ceremony::where('status','1')
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
                        $ceremonies = Ceremony::where('status','1')
                            ->whereRaw("FIND_IN_SET($filter_type,ceremony_for)")
                            ->get();

                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else if($filter_by=='faculty'){

                        $ceremonies = Ceremony::where('status','1')
                            ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                            ->get();


                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else{
                        $ceremonies = Ceremony::where('status','1')
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


    public function ceremonyList2(Request $request){

        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

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
                'session_token'         => 'required',
                'language_id'         => 'required',
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
            $filter_by = $formData['filter_by'];
            $filter_type = $formData['filter_type'];
            $language_id = $formData['language_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);

            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{

                $sessions_data  =  DB::table('sessions')->where('user_id',$user_id)->first();
                $userId 		=  $sessions_data->user_id;

                $userdetail  	=  DB::table('users')->where('id',$userId)->first();

                if(!empty($search)){

                    $ceremonies  = Ceremony::with('bookings')
                        //->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        //	->where('u.language_id',$language_id)
                        //	->where(function ($query) use ($userdetail) {
                        //  $query->where('ceremony_for', '=', 2)
                        //		  ->whereDate('date', '>=' ,date("Y-m-d"))
                        //       ->orWhere('ceremony_for', '=', $userdetail->gender);
                        //	})
                        ->where('name','like', '%' . $search . '%')
                        ->where('status','1')
                        ->get();
                    //->select('ceremony.id','name','u.description','total_seats','minimum_downpayment_amount','price','ceremony_price','free_seats','date','status','u.address','u.latitude','u.longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(100);

                    foreach ($ceremonies as $key => $value) {

                        $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                        $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                        $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                        $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                        $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                        $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                        $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                        if(!empty($value->bookings)){
                            $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                            $rms = $value->total_seats - $sum;
                            $value->remaining_seats = $rms < 0 ? 0 : $rms;
                        }else{
                            $value->remaining_seats = 0;
                        }

                        $booking_data=$value->bookings;
                        foreach($booking_data as $val)
                        {
                            $val->id=(int)$val->id;
                            $val->user_id=(int)$val->user_id;
                            $val->event_id=(int)$val->event_id;
                            $val->no_of_seats=(int)$val->no_of_seats;
                            $val->amount=(int)$val->amount;
                            $val->remaining_amount=(int)$val->remaining_amount;

                        }
                    }



                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );
                }else{

                    if($filter_by=='gender'){

                        $ceremonies = Ceremony::with('bookings')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->whereRaw("FIND_IN_SET($filter_type,ceremony_for)")
                            ->whereDate('date', '>=' ,date("Y-m-d"))
                            ->select('ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats','minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats',
                                'date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for')
                            ->get();

                        foreach ($ceremonies as $key => $value) {
                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }
                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }
                        }


                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else if($filter_by=='faculty'){

                        $ceremonies = Ceremony::with('bookings')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                            ->where('date', '>=',date("Y-m-d"))->select('ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats',
                                'minimum_downpayment_amount',
                                'price',
                                'ceremony_price',
                                'free_seats',
                                'number_of_students',
                                'date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for')
                            ->get();

                        // $ceremonies  = Ceremony::where(function ($query) use ($filter_type)  {
                        // 			    $query->whereRaw("FIND_IN_SET($filter_type,faculty)")
                        //                                            ->where('date', '>=' ,date("Y-m-d"));
                        // 			})->select('id','name','description','total_seats','minimum_downpayment_amount','remaining_seats','price','date','status','address','latitude','longitude','image', 'ceremony_for','faculty')->orderBy($sort_by, $sort_type)->paginate(10);

                        foreach ($ceremonies as $key => $value) {
                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }

                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }
                        }

                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else{
                        /*$ceremonies = ceremony::where(function ($query) use ($gender) {
                                            $query->where('ceremony_for', '=', 2)
                                                  ->whereDate('date', '>=' ,date("Y-m-d"))
                                                  //->where('date','>=',)
                                                  ->orWhere('ceremony_for', '=', $gender);
                                        })->select('id','name','description','total_seats','minimum_downpayment_amount','remaining_seats','price','date','status','address','latitude','longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(10);*/


                        $ceremonies  = Ceremony::with('bookings')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->where(function ($query) use ($userdetail) {
                                $query->whereDate('date', '>=' ,date("Y-m-d"));


                            })->select(
                                'ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats',
                                'minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats','date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for',
                                'faculty')->orderBy($sort_by, $sort_type)->paginate(100);

                        foreach ($ceremonies as $key => $value) {

                            /*echo "eventidhwe".$value->id;
                            echo "<br>";
                            echo "useridhwe".$user_id;*/
                            $user_event_details=Booking::where('user_id',$user_id)->where('event_id',$value->id)->first();

                            $ceremony_status='';
                            //check seats boooking
                            if($user_event_details==''){

                                $ceremony_status = 1;
                            }
                            else{

                                $payment_type=$user_event_details->payment_type;

                                if($payment_type=='Down' || $payment_type=='down')
                                {
                                    if($user_event_details->ceremony_price=='')
                                    {
                                        $ceremony_status = 1;
                                    }

                                }
                                else
                                {
                                    $ceremony_status = 0;
                                }
                            }


                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;

                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            $ceremonies[$key]->number_of_students = (int)$ceremonies[$key]->number_of_students;


                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;


                            }else{
                                $value->remaining_seats = 0;
                            }
                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

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
        }
        return Response::json($response);
        //return Response::json_encode( $response, JSON_NUMERIC_CHECK );
        die;
    }

    public function ceremonyList4(Request $request){

        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

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
                'session_token'         => 'required',
                'language_id'         => 'required',
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
            $filter_by = $formData['filter_by'];
            $filter_type = $formData['filter_type'];
            $language_id = $formData['language_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);

            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{

                $sessions_data  =  DB::table('sessions')->where('user_id',$user_id)->first();
                $userId 		=  $sessions_data->user_id;

                $userdetail  	=  DB::table('users')->where('id',$userId)->first();

                if(!empty($search)){

                    $ceremonies  = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                        //->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        //	->where('u.language_id',$language_id)
                        //	->where(function ($query) use ($userdetail) {
                        //  $query->where('ceremony_for', '=', 2)
                        //		  ->whereDate('date', '>=' ,date("Y-m-d"))
                        //       ->orWhere('ceremony_for', '=', $userdetail->gender);
                        //	})
                        ->where('name','like', '%' . $search . '%')
                        ->where('status','1')
                        ->get();
                    //->select('ceremony.id','name','u.description','total_seats','minimum_downpayment_amount','price','ceremony_price','free_seats','date','status','u.address','u.latitude','u.longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(100);

                    foreach ($ceremonies as $key => $value) {

                        $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                        $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                        $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                        $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                        $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                        $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                        $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                        if(!empty($value->bookings)){
                            $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                            $rms = $value->total_seats - $sum;
                            $value->remaining_seats = $rms < 0 ? 0 : $rms;
                        }else{
                            $value->remaining_seats = 0;
                        }

                        $booking_data=$value->bookings;
                        foreach($booking_data as $val)
                        {
                            $val->id=(int)$val->id;
                            $val->user_id=(int)$val->user_id;
                            $val->event_id=(int)$val->event_id;
                            $val->no_of_seats=(int)$val->no_of_seats;
                            $val->amount=(int)$val->amount;
                            $val->remaining_amount=(int)$val->remaining_amount;

                        }
                    }



                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );
                }else{

                    if($filter_by=='gender'){

                        $ceremonies = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->whereRaw("FIND_IN_SET($filter_type,ceremony_for)")
                            ->whereDate('date', '>=' ,date("Y-m-d"))
                            ->select('ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats','minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats',
                                'date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for')
                            ->get();

                        foreach ($ceremonies as $key => $value) {
                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }
                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }
                        }


                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else if($filter_by=='faculty'){

                        $ceremonies = Ceremony::with('bookings:event_id,user_id,no_of_seats')->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                            ->where('date', '>=',date("Y-m-d"))->select('ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats',
                                'minimum_downpayment_amount',
                                'price',
                                'ceremony_price',
                                'free_seats',
                                'number_of_students',
                                'date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for')
                            ->get();

                        // $ceremonies  = Ceremony::where(function ($query) use ($filter_type)  {
                        // 			    $query->whereRaw("FIND_IN_SET($filter_type,faculty)")
                        //                                            ->where('date', '>=' ,date("Y-m-d"));
                        // 			})->select('id','name','description','total_seats','minimum_downpayment_amount','remaining_seats','price','date','status','address','latitude','longitude','image', 'ceremony_for','faculty')->orderBy($sort_by, $sort_type)->paginate(10);

                        foreach ($ceremonies as $key => $value) {

                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }

                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }

                        }

                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else{
                        /*$ceremonies = ceremony::where(function ($query) use ($gender) {
                                            $query->where('ceremony_for', '=', 2)
                                                  ->whereDate('date', '>=' ,date("Y-m-d"))
                                                  //->where('date','>=',)
                                                  ->orWhere('ceremony_for', '=', $gender);
                                        })->select('id','name','description','total_seats','minimum_downpayment_amount','remaining_seats','price','date','status','address','latitude','longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(10);*/


                        $ceremonies  = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->where(function ($query) use ($userdetail) {
                                $query->whereDate('date', '>=' ,date("Y-m-d"));


                            })->select(
                                'ceremony.id',
                                'u.name',
                                'u.description',
                                'terms',
                                'total_seats',
                                'minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats','date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for',
                                'faculty')->orderBy($sort_by, $sort_type)->paginate(100);

                        foreach ($ceremonies as $key => $value) {

                            /*echo "eventidhwe".$value->id;
                            echo "<br>";
                            echo "useridhwe".$user_id;*/
                            $user_event_details=Booking::where('user_id',$user_id)->where('event_id',$value->id)->first();

                            $ceremony_status='';
                            //check seats boooking
                            if($user_event_details==''){

                                $ceremony_status = 1;
                            }
                            else{

                                $payment_type=$user_event_details->payment_type;

                                if($payment_type=='Down' || $payment_type=='down')
                                {
                                    if($user_event_details->ceremony_price=='')
                                    {
                                        $ceremony_status = 1;
                                    }

                                }
                                else
                                {
                                    $ceremony_status = 0;
                                }
                            }


                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;

                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            $ceremonies[$key]->number_of_students = (int)$ceremonies[$key]->number_of_students;


                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;


                            }else{
                                $value->remaining_seats = 0;
                            }
                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

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
        }
        return Response::json($response);
        //return Response::json_encode( $response, JSON_NUMERIC_CHECK );
        die;
    }

    public function ceremonyList3(Request $request){

        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'ceremony.date';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

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
                'session_token'         => 'required',
                'language_id'         => 'required',
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
            $filter_by = $formData['filter_by'];
            $filter_type = $formData['filter_type'];
            $language_id = $formData['language_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);

            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{

                $sessions_data  =  DB::table('sessions')->where('user_id',$user_id)->first();
                $userId 		=  $sessions_data->user_id;

                $userdetail  	=  DB::table('users')->where('id',$userId)->first();

                if(!empty($search)){

                    $ceremonies  = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                        //->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        //	->where('u.language_id',$language_id)
                        //	->where(function ($query) use ($userdetail) {
                        //  $query->where('ceremony_for', '=', 2)
                        //		  ->whereDate('date', '>=' ,date("Y-m-d"))
                        //       ->orWhere('ceremony_for', '=', $userdetail->gender);
                        //	})
                        ->where('name','like', '%' . $search . '%')
                        ->where('status','1')
                        ->get();
                    //->select('ceremony.id','name','u.description','total_seats','minimum_downpayment_amount','price','ceremony_price','free_seats','date','status','u.address','u.latitude','u.longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(100);

                    foreach ($ceremonies as $key => $value) {

                        $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                        $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                        $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                        $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                        $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                        $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                        $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                        if(!empty($value->bookings)){
                            $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                            $rms = $value->total_seats - $sum;
                            $value->remaining_seats = $rms < 0 ? 0 : $rms;
                        }else{
                            $value->remaining_seats = 0;
                        }

                        $booking_data=$value->bookings;
                        foreach($booking_data as $val)
                        {
                            $val->id=(int)$val->id;
                            $val->user_id=(int)$val->user_id;
                            $val->event_id=(int)$val->event_id;
                            $val->no_of_seats=(int)$val->no_of_seats;
                            $val->amount=(int)$val->amount;
                            $val->remaining_amount=(int)$val->remaining_amount;

                        }
                    }



                    $response = array(
                        'status' 	=> 1,
                        'message'	=> "Success",
                        'ceremonies' => $ceremonies->all(),
                    );
                }else{

                    if($filter_by=='gender'){

                        $ceremonies = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->whereRaw("FIND_IN_SET($filter_type,ceremony_for)")
                            ->whereDate('date', '>=' ,date("Y-m-d"))
                            ->select('ceremony.id',
                                'u.name',
                                //	'u.description',
                                'code',
                                'imageterm',
                                'total_seats','minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats',
                                'date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for')
                            ->get();

                        foreach ($ceremonies as $key => $value) {
                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }
                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }
                        }


                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else if($filter_by=='faculty'){

                        $ceremonies = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                            //	->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            //	->where('u.language_id',$language_id)
                            ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                            ->where('status','1')
                            ->get();
                        /*
                    $ceremonies = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                        ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                        ->where('u.language_id',$language_id)
                        ->where('status','1')

                        ->whereRaw("FIND_IN_SET($filter_type,faculty)")
                        ->where('date', '>=',date("Y-m-d"))->select('ceremony.id',
                        'u.name',
                    //	'u.description',
                        'code',
                        'imageterm',
                        'total_seats',
                        'minimum_downpayment_amount',
                        'price',
                        'ceremony_price',
                        'free_seats',
                        'number_of_students',
                        'date',
                        'status',
                        'hashtag',
                        'u.address',
                        'u.latitude',
                        'u.longitude',
                        'image',
                        'imagemain',
                        'imagedes',
                        'ceremony_for')
                    //	->get();
                        ->paginate(100);
*/
                        /*	 $ceremonies  = Ceremony::where(function ($query) use ($filter_type)  {
                                             $query->whereRaw("FIND_IN_SET($filter_type,faculty)")
                                                   ->where('date', '>=' ,date("Y-m-d"));
                                        })->select(
                            'id',
                            'name',
                            'description',
                            'total_seats',
                            'terms',
                            'minimum_downpayment_amount',
                            'remaining_seats',
                            'price',
                            'date',
                            'status',
                            'address',
                            'latitude',
                            'longitude',
                            'image',
                            'ceremony_for',
                            'imagemain',
                                'imagedes',
                            'faculty')->orderBy($sort_by, $sort_type)->paginate(10);*/

                        foreach ($ceremonies as $key => $value) {
                            if(sizeof($value->bookings) >= $value->free_seats ){
                                $ceremony_status = 0;
                            }
                            else{
                                $ceremony_status = 1;
                            }

                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;
                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;
                            }else{
                                $value->remaining_seats = 0;
                            }

                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

                            }
                        }

                        $response = array(
                            'status' 	=> 1,
                            'message'	=> "Success",
                            'ceremonies' => $ceremonies->all(),
                        );

                    }
                    else{
                        /*$ceremonies = ceremony::where(function ($query) use ($gender) {
                                            $query->where('ceremony_for', '=', 2)
                                                  ->whereDate('date', '>=' ,date("Y-m-d"))
                                                  //->where('date','>=',)
                                                  ->orWhere('ceremony_for', '=', $gender);
                                        })->select('id','name','description','total_seats','minimum_downpayment_amount','remaining_seats','price','date','status','address','latitude','longitude','image', 'ceremony_for')->orderBy($sort_by, $sort_type)->paginate(10);*/


                        $ceremonies  = Ceremony::with('bookings:event_id,user_id,no_of_seats')
                            ->join('ceremony_description as u', 'ceremony.id', '=', 'u.parent_id')
                            ->where('u.language_id',$language_id)
                            ->where('status','1')

                            ->where(function ($query) use ($userdetail) {
                                $query->whereDate('date', '>=' ,date("Y-m-d"));


                            })->select(
                                'ceremony.id',
                                'u.name',
                                // 'u.description',
                                'code',
                                'imageterm',
                                'total_seats',
                                'minimum_downpayment_amount',
                                'price',
                                'number_of_students',
                                'ceremony_price',
                                'free_seats','date',
                                'status',
                                'hashtag',
                                'u.address',
                                'u.latitude',
                                'u.longitude',
                                'image',
                                'imagemain',
                                'imagedes',
                                'ceremony_for',
                                'faculty')->orderBy($sort_by, $sort_type)->paginate(100);

                        foreach ($ceremonies as $key => $value) {

                            /*echo "eventidhwe".$value->id;
                            echo "<br>";
                            echo "useridhwe".$user_id;*/
                            $user_event_details=Booking::where('user_id',$user_id)->where('event_id',$value->id)->first();

                            $ceremony_status='';
                            //check seats boooking
                            if($user_event_details==''){

                                $ceremony_status = 1;
                            }
                            else{

                                $payment_type=$user_event_details->payment_type;

                                if($payment_type=='Down' || $payment_type=='down')
                                {
                                    if($user_event_details->ceremony_price=='')
                                    {
                                        $ceremony_status = 1;
                                    }

                                }
                                else
                                {
                                    $ceremony_status = 0;
                                }
                            }


                            $ceremonies[$key]->ceremony_status = $ceremony_status;

                            $ceremonies[$key]->id = (int)$ceremonies[$key]->id;
                            $ceremonies[$key]->price = (int)$ceremonies[$key]->price;
                            $ceremonies[$key]->status = (int)$ceremonies[$key]->status;
                            $ceremonies[$key]->minimum_downpayment_amount = (double)$ceremonies[$key]->minimum_downpayment_amount;
                            $ceremonies[$key]->ceremony_for = (int)$ceremonies[$key]->ceremony_for;
                            $ceremonies[$key]->ceremony_price = (double)$ceremonies[$key]->ceremony_price;

                            $ceremonies[$key]->free_seats = (int)$ceremonies[$key]->free_seats;
                            $ceremonies[$key]->total_seats = (int)$ceremonies[$key]->total_seats;
                            $ceremonies[$key]->number_of_students = (int)$ceremonies[$key]->number_of_students;


                            if(!empty($value->bookings)){
                                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                                $rms = $value->total_seats - $sum;
                                $value->remaining_seats = $rms < 0 ? 0 : $rms;


                            }else{
                                $value->remaining_seats = 0;
                            }
                            $booking_data=$value->bookings;
                            foreach($booking_data as $val)
                            {
                                $val->id=(int)$val->id;
                                $val->user_id=(int)$val->user_id;
                                $val->event_id=(int)$val->event_id;
                                $val->no_of_seats=(int)$val->no_of_seats;
                                $val->amount=(int)$val->amount;
                                $val->remaining_amount=(int)$val->remaining_amount;

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
        }
        return Response::json($response);
        //return Response::json_encode( $response, JSON_NUMERIC_CHECK );
        die;
    }

    public function bookCeremonySeats(Request $request){
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(
            'user_id' 		=> "Please Enter User ID",
            'session_token' => "Please Enter Session Token",
            'ceremony_id' 	=> "Please Enter Ceremony Id",
            'price' 		=> "Please Enter Price",
            'discount' 		=> "Please Enter Discount",
            'final_price' 	=> "Please Enter Final Price",
            'seats' 		=> "Please Enter No. Of Seats",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'user_id' 			=> 'required',
                'session_token'     => 'required',
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

            $user_id = $formData['user_id'];
            $session_token =  $formData['session_token'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{
                $ceremony_id = $formData['ceremony_id'];
                $price = $formData['price'];
                $discount = $formData['discount'];
                $final_price = $formData['final_price'];
                $seats = $formData['seats'];
                $promocode_id = $formData['promocode_id'] ? $formData['promocode_id'] : 0;

                $ceremony_detail = Ceremony::where('id',$ceremony_id)->select('id','name','total_seats','remaining_seats','price','date')->first();
                $todayDate = date('Y-m-d');

                $userInfo = User::where('id',$user_id)->first();

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
                            $obj 					=  new Booking;
                            $obj->user_id			=  $user_id;
                            $obj->ceremony_id 		=  $ceremony_id;
                            $obj->booking_no 		=  $booking_no;
                            $obj->slug	 			=  $this->getSlug($booking_no,'slug','Booking');
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

                            $mail			= $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);



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
        }

        $response =	array(
            'status' 	=> $status,
            'message'	=> $message,
            'detail'   => $detail,
        );

        return Response::json($response); die;
    }

    public function booking(Request $request){

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
                'detail'    => $detail
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
                        'detail'    => $detail
                    );
                    return Response::json($response); die;
                }


                $payment_details=DB::table('payment_log')->select('*')->where('tranid',$trans_id)->first();

                if($payment_details->result!='CAPTURED')
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'detail'    => $detail*/
                    );
                }
                else
                {

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

                    $first_booking=Booking::select('ceremony_price')->where('user_id',$user_id)->where('event_id',$event_id)->first();

                    if($first_booking!=''){
                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'لا يمكن الحجز أكثر من مره',
                        );

                        return Response::json($response); die;
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

                            return Response::json($response); die;
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

                            return Response::json($response); die;
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
                            /*'detail'    => $detail*/
                        );
                    }
                }

            }
        }
        return Response::json($response); die;
    }

    public function updatebooking(Request $request){

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
                'detail'    => $detail
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
                        'detail'    => $detail
                    );
                    return Response::json($response); die;
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

                        return Response::json($response); die;
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

                        return Response::json($response); die;
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
                    $onebooking->session_token		=  $session_token;
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
                            /*'detail'    => $detail*/
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
                'detail'    => $detail
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
                        'detail'    => $detail
                    );
                    return Response::json($response); die;
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

                    return Response::json($response); die;
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

                        return Response::json($response); die;
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

                        return Response::json($response); die;
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
        return Response::json($response); die;
    }


    public function bookingEdit(Request $request){


        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(
            'booking_id.required' => "Please Enter Booking ID",
            'user_id.required'  => "Please Enter User ID",
            'session_token.required'	=> "Please Enter Session ID",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'booking_id' => 'required',
                'user_id'  => 'required',
                'session_token'	=> 'required',

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
                'detail'    => $detail
            );

        }

        else{


            $user_id = $formData['user_id'];
            $booking_id = $formData['booking_id'];
            $seats = $formData['no_of_seats'];
            $session_token =  $formData['session_token'];
            $trans_id 	= $formData['trans_id'];
            $robe_size 	= $formData['rob_size'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }


            $checkseat_detail = DB::table('booking')->find($booking_id);

            $isSeatAvailable = $this->checkSeatAvailabilityForEvent($checkseat_detail->event_id, $seats);
            //::where('id',$event_id)->select('remaining_seats')->first();
            if(!empty($checkseat_detail)){

                if(!$isSeatAvailable){
                    $response	=	array(
                        'status' 	=>  0,
                        'message'	=> 'Seats are not available.',
                        'detail'    => $detail
                    );
                    return Response::json($response); die;
                }

                if(empty($trans_id))
                {
                    Booking::where('id',$booking_id)->update(['robe_size' => $robe_size]);

                    $response	=	array(
                        'status' 	=> 1,
                        'message'	=> "Booking Updated Successfully",
                        /*'detail'   => $data,*/

                    );
                    return Response::json($response);
                    die;

                }
                $booking_details=DB::table('booking')->select('*')->where('id',$booking_id)->first();
                $booking_user=$booking_details->user_id;
                $ceremony_id=$booking_details->event_id;
                $prive_amount=$booking_details->amount;
                $pri_no_of_seats=$booking_details->no_of_seats;

                $ceremony_details=DB::table('ceremony')->select('*')->where('id',$ceremony_id)->first();
                $price=$ceremony_details->price;
                $allseats=$ceremony_details->total_seats;
                $new_amout=$price*$seats;

                $total_seats=$pri_no_of_seats+$seats;
                $total_price=$prive_amount+$new_amout;

                $payment_details=DB::table('payment_log')->select('*')->where('tranid',$trans_id)->first();

                if($payment_details->result!='CAPTURED' || $payment_details->amt!=$new_amout)
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'detail'    => $detail*/
                    );
                }
                else
                {
                    if(empty($robe_size))
                    {
                        $results=Booking::where('id',$booking_id)->update(['no_of_seats' => $total_seats,'amount'=>$total_price]);
                    }
                    else
                    {
                        $results=Booking::where('id',$booking_id)->update(['no_of_seats' => $total_seats,'amount'=>$total_price,'robe_size'=>$robe_size]);
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
                        /*'detail'   => $data,*/

                    );

                }
            }
        }

        return Response::json($response);
        die;
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

            $response=array('status'=>0,'message'=>$allErrors,'detail'=> $detail);
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
                    /*'detail'   => $data,*/

                );
            }
            else
            {
                $response	=	array(
                    'status' 	=> 0,
                    'message'	=> 'Unsuccessful',
                    /*'detail'	=> $detail*/
                );
            }

        }

        return Response::json($response);
        die;

    }


    public function addBookingPayment(Request $request){

        $formData	= $request->all();

        $detail = (object) null;

        $response	= array();
        $messages = array(
            'user_id.required'  => "Please Enter User ID",
            'session_token.required'	=> "Please Enter Session ID",
            'bookingid.required' => "Please Enter Booking ID",
            'trans_id.required' => "Please Enter Trans Id",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'user_id'  => 'required',
                'session_token'	=> 'required',
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
                'detail'    => $detail
            );

        }

        else{
            $user_id = $formData['user_id'];
            $bookingid = $formData['bookingid'];
            $session_token =  $formData['session_token'];
            $trans_id = $formData['trans_id'];


            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }

            $booking = Booking::select('*')->where('id',$bookingid)->first();


            if(!empty($booking->remaining_amount)){

                $payment_details=DB::table('payment_log')->select('*')->where('tranid',$trans_id)->first();

                if($payment_details->result!='CAPTURED')
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'فشل الاجراء',
                        /*'detail'    => $detail*/
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
                        'message'	=> 'Event Seat Book Successfully.',
                        'transationid'=>$payment_details->tranid,
                        'paymentID'=>$payment_details->paymentid,
                        'amount'=>$payment_details->amt,
                        'created_at'=>$payment_details->created_at
                        /*'detail'    => $detail*/
                    );

                }

            }
            else
            {
                $response	=	array(
                    'status' 	=>  0,
                    'message'	=> 'The remaining amount cannot be greater than 0.',

                );
                return Response::json($response); die;

            }
        }
        return Response::json($response); die;
    }

    public function changerobesize(Request $request){


        $detail = (object) null;

        $response	= array();
        $messages = array(
            'event_id.required' => "Please Enter Event ID",
            'user_id.required'  => "Please Enter User ID",
            'session_token.required'	=> "Please Enter Session ID",
            'robe_size.required'	=> "Please Select Robe_size",

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'user_id'  => 'required',
                'session_token'	=> 'required',
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
                'detail'    => $detail
            );

        }else{

            $event_id=$request->event_id;
            $user_id=$request->user_id;
            $session_token=$request->session_token;
            $robe_size=$request->robe_size;


            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }

            // $result = DB::table('booking')
            //	->where('user_id',$user_id)
            //	->where('event_id',$event_id)
            //	->get();

            //	return Response::json($result); die;

            DB::table('booking')
                ->where('user_id',$user_id)
                ->where('event_id',$event_id)
                ->update(['robe_size' => $robe_size]);

            $response	=	array(
                'status' 	=>  1,
                'message'	=> 'تم تعديل مقاس الروب'
            );
            return Response::json($response); die;

        }
        return Response::json($response); die;

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

    public function bookingList(Request $request){
        $search     	= $request->name;
        $sort_by     	= $request->sort_by ? $request->sort_by : 'booking.id';
        $sort_type     	= $request->sort_type ? $request->sort_type : 'desc';

        $formData	= $request->all();
        $detail = (object) null;
        $messages = array(
            'user_id' 		    => "Please Enter User ID",
            'session_token' 	=> "Please Enter Session Token",
            'language_id' 		=> "Please Enter language_id",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'user_id' 			=> 'required',
                'session_token'		=> 'required',
                'language_id'		=> 'required',
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
        }

        else{

            $user_id = $formData['user_id'];
            $session_token =  $formData['session_token'];
            $language_id =  $formData['language_id'];

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }
            else{

                \Session::put("lang",$language_id);


                /* $booking = Ceremony::leftJoin('booking', function($join) {
                              $join->on('booking.event_id', '=', 'ceremony.id');
                            })->where('user_id',$user_id)->orderBy($sort_by, $sort_type)->get(); */


                $booking = Booking::with('ceremony' , 'ceremonyWithDescription','payments')
                    ->where('user_id',$user_id)->orderBy('id','desc')->paginate()->toArray();

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
                $data= $booking['data'];

                foreach($data as $value)
                {
                    $id = (int)$value['id'];
                    $user_id = (int)$value['user_id'];

                    $event_id = (int)$value['event_id'];
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
                        "session_token"=>$session_token,
                        "enroll_amt"=>$enroll_amt,
                        "created_at"=>$created_at,
                        "updated_at"=>$updated_at,
                        "ceremony_with_description"=>$ceremony_data,
                        "payments"=>$payment_data));

                }

                $booking_responce= array("total"=>$total,"per_page"=>$per_page,"current_page"=>$current_page,"last_page"=>$last_page,"next_page_url"=>$next_page_url,"prev_page_url"=>$prev_page_url,"from"=>$from,"to"=>$to,"data"=>$data_details);
                /*foreach($booking['data'] as $rsKey => $rs){


                    foreach($rs as $key => $value){


                        if(is_null($value)){
                            $booking['data'][$rsKey][$key] = new \stdClass();
                        }


                         $booking['data'][$rsKey]['id']= (int)$booking['data'][$rsKey]['id'];

                        $booking['data'][$rsKey]['user_id']= (int)$booking['data'][$rsKey]['user_id'];

                        $booking['data'][$rsKey]['event_id']= (int)$booking['data'][$rsKey]['event_id'];

                        $booking['data'][$rsKey]['amount']= (int)$booking['data'][$rsKey]['amount'];

                       $booking['data'][$rsKey]['no_of_seats']= (int)$booking['data'][$rsKey]['no_of_seats'];


                        $booking['data'][$rsKey]['remaining_amount']= (double)$booking['data'][$rsKey]['remaining_amount'];



                    }



                }*/


                if(empty($booking)){
                    $response =array(
                        'status'=> 1,
                        'messages'=> 'Booking List',
                        'detail'=>$booking_responce,
                    );
                }else{
                    $response =array(
                        'status'=> 1,
                        'messages'=> 'Booking List',
                        'detail'=>$booking_responce,
                    );
                }
            }

        }

        return Response::json($response);
        //return Response::json($response, 200, [], JSON_NUMERIC_CHECK);
    }



    public function ceremonydetail(Request $request){

        //	$ceremony = Ceremony::where('status','1')
        //		->whereRaw("FIND_IN_SET($filter_type,faculty)")
        //	->get();
        ////
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(
            'user_id' 		    => "Please Enter User ID",
            'session_token' 	=> "Please Enter Session Token",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'user_id' 			=> 'required',
                'session_token'         => 'required',
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

            $checkUserSession = $this->verifyUserSession($user_id, $session_token);
            if(is_array($checkUserSession)){
                return  Response::json($checkUserSession);
            }else{

                $event_id = $request->event_id ;

                $ceremony = Ceremony::where('id',$event_id)
                    ->get();

                //	 return Response::json($ceremony);


                $bookinglist = Booking::where('event_id',$event_id)
                    ->get();

                if(sizeof($bookinglist) >= $ceremony[0]->free_seats ){
                    $ceremony_status = 0;
                }
                else{
                    $ceremony_status = 1;
                }
                $ceremony[0]->ceremony_status = $ceremony_status;
                $ceremony[0]->id = (int)$ceremony[0]->id;
                $ceremony[0]->price = (int)$ceremony[0]->price;
                $ceremony[0]->status = (int)$ceremony[0]->status;
                $ceremony[0]->minimum_downpayment_amount = (double)$ceremony[0]->minimum_downpayment_amount;
                $ceremony[0]->ceremony_for = (int)$ceremony[0]->ceremony_for;
                $ceremony[0]->ceremony_price = (double)$ceremony[0]->ceremony_price;
                $ceremony[0]->free_seats = (int)$ceremony[0]->free_seats;
                $ceremony[0]->total_seats = (int)$ceremony[0]->total_seats;
                $ceremony[0]->number_of_booking_students = (int)sizeof($bookinglist);


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
            }
        }
        return Response::json($response);


    }

    public function onebookingList(Request $request){
        $eventid 	= $request->event_id;
        $user_id   	= $request->user_id;
        $response	= array();

        $onebooking = Booking::with("ceremony")->where('user_id',$user_id)
            ->where('event_id',$eventid)
            ->get();




        if(empty($onebooking)){
            $response=array(
                'status'=>0,
                'message'=> 'Booking List',
                'data'=> $onebooking);
        }else{
            $response=array(
                'status'=>1,
                'message'=> 'Booking List',
                'data'=> $onebooking);
        }

        return Response::json($response);


    }

    public function checkSeatAvailability(Request $request){
        $eventid 	= $request->event_id;
        $seats   	= $request->seat;

        //	$ceremonies = Ceremony::with('bookings:id,event_id')->where('id',$eventid)
        //		->select('id','total_seats')->get();

        //	$ceremonies = Ceremony::with(array('bookings'=>function($query){
        //   $query->select('id','user_id', 'event_id');
        //  }))->where('id',$eventid)
        //	->select('id','total_seats')->get();

        $ceremonies = Ceremony::with('booking')->where('id',$eventid)
            ->select('id','total_seats')->get();

        $response =array(
            'status'=> 1,
            'detail'=>$ceremonies,
        );
        return Response::json($response);

        foreach ($ceremonies as $key => $value) {
            if(!empty($value->booking)){
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
        $userId 		=	$request->user_id;
        $seat 			=	$request->no_of_seat;
        $amount 		=	$request->amount;
        $robeSize 		=	$request->robe_size;
        $sessionToken 	=	$request->session_token;

        $response	= 	array();

        $messages   = array(
            'event_id.required' => "Please Enter Event ID",
            'user_id.required'   => "Please Enter User ID",
            'session_token.required'	=> "Please Enter Session ID",
            'no_of_seats.required'	=> "Please Enter No. of Seats",
            'amount.required' => "Please Enter Amount",
            'robe_size.required'=> "Please Enter Robe Size"
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'event_id' => 'required',
                'user_id'  => 'required',
                'session_token'	=> 'required',
                'robe_size'	=> 'required',
                'no_of_seats'	=> 'required|min:1|integer',
                'amount' => 'required|numeric|between:0,99999.99'
            ), $messages
        );


        $checkUserSession = $this->verifyUserSession($userId, $sessionToken);
        if(is_array($checkUserSession)){
            return  Response::json($checkUserSession);
        }

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

        return Response::json($response);

    }

    public function booking_csvfile(Request $request)
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

            $response=array('status'=>0,'message'=>$allErrors,'detail'=> $detail);

            return Response::json($response);
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
                $user = User::where('id',$results->user_id)->first();
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

            $response=array('status'=>0,'message'=>$allErrors,'detail'=> $detail);
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
            }

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

                    $user = User::where('id',$value->user_id)->first();
                    $items = Ceremony::where('id',$value->event_id)->first();

                    fputcsv($file, array($value->id, $items->name, $user->full_name, $value->no_of_seats, $value->payment_type, $value->amount, $value->remaining_amount,$value->robe_size));
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

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