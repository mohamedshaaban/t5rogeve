<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResources;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\DeviceInfo;
use Backpack\NewsCRUD\app\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Infobip\Configuration;
use Auth;
class RegisterController extends Controller
{
    public static function updateToken(Request $request)
    {
        $customer = Auth::guard('customers_api')->user();
        $user_id = $customer->id;

        $device_info =array(
            'user_id'    	 => $customer->id,
            'device_id'      => $request->device_id,
            'device_type'    => $request->device_type,
            'device_token'   => $request->device_token,
        );
        DeviceInfo::where('user_id',$user_id)->delete();

        DeviceInfo::create($device_info);
        $response	=	array(
            'status' 	=> 1,
            'message'	=> 'تم التعديل',
         );
        return $response;

    }
    public static function customerRegister(Request $request)
    {
       
        $validator = Validator::make(
            $request->all(),
            array(
                'phone' 			=> 'required|unique:customers',
                'gender' 			=> 'required',
                'date_of_birth' 			=> 'required',
                'email' 			=> 'email|unique:customers',
                'faculty'			=> 'required',
                'password'			=> 'required|min:6',
                'civilid'			=> 'length:12',
                'device_id'			=> 'required',
                'device_type'		=> 'required',

            )
        );
        if ($validator->fails())
        {
            $allErrors =  [];
            foreach ($validator->errors()->all() as $message){
                $allErrors =  $message;
            }

            $response	=	array(
                'status' 	=> 0,
                'message'	=> $allErrors,
                'detail'    => ''
            );
            return $response ;

        }
        else
        {
            $userRoleId				=  2  ;
            $otp = mt_rand(1000, 9999);
            $name = $request->phone;
            if($request->email && $request->email !='')
            {
                $email = explode('@',$request->email);
                $name = $email[0];

            }
            $fullName			=  ucwords($request->full_name);


            $customer = new Customer();
            $customer->email 			=  $request->email;
            $customer->slug	 			=  Str::slug($name, "-");
            $customer->password	 		=  Hash::make($request->password);
            $customer->user_role_id		=  $userRoleId;
            $customer->gender			=  $request->gender;
            $customer->phone				=  $request->phone;
            $customer->civil_id			=  $request->civil_id;
            $customer->faulty			=  $request->faculty;
            $customer->date_of_birth			=  $request->date_of_birth;
            $customer->otp = $otp;
            $customer->is_verified      = 0;
            $customer->save();
            $device_info =array(
                'user_id'    	 => $customer->id,
                'device_id'      => $request->device_id,
                'device_type'    => $request->device_type,
                'device_token'   => $request->device_token,
            );

           DeviceInfo::create($device_info);
            $response	=	array(
                'status' 	=> 1,
                'message'	=> 'يرجى إدخال رمز التحقق الذي تلقيته على رقم الموبايل',
                'detail'    => ''
            );
            $url_address = "http://www.kwtsms.com/API/send/?";
            $username = "academy";
            $password = "rR99156852$";
            $sender = "ACADEMY";
            $lang = "2";
            $digits = 4;
            //$code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $to = "+965".$request->phone;

            $userInfo = Customer::where('phone',$request->phone)->first();
            $otp = $userInfo->otp;
            $text = 'Your Code :'.$otp;


 
            $service_plan_id = "34dd23f6844a47e082eaf55ea3ac8fa4";
            $bearer_token = "128b52a28c6e42d9b9852495c6a3008d";
            
            //Any phone number assigned to your API
            $send_from = "447537454598";
            //May be several, separate with a comma ,
            $recipient_phone_numbers = $to; 
            $message =$text;
            
            // Check recipient_phone_numbers for multiple numbers and make it an array.
            if(stristr($recipient_phone_numbers, ',')){
              $recipient_phone_numbers = explode(',', $recipient_phone_numbers);
            }else{
              $recipient_phone_numbers = [$recipient_phone_numbers];
            }
            
            // Set necessary fields to be JSON encoded
            $content = [
              'to' => array_values($recipient_phone_numbers),
              'from' => $send_from,
              'body' => $message
            ];
            
            $data = json_encode($content);
            
            $ch = curl_init("https://us.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
            curl_setopt($ch, CURLOPT_XOAUTH2_BEARER, $bearer_token);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $result = curl_exec($ch);
            
            if(curl_errno($ch)) {
                // echo 'Curl error: ' . curl_error($ch);
            } else {
                // echo $result;
            }
            curl_close($ch);
                    
             $response	=	array(
                'status' 	=> 1,
                'message'	=> 'تم انشاء الحساب',
                'detail'    => new CustomerResources($customer)
            );
        }


        return  ($response);

    }

    public static function customerVerifiy(Request $request)
    {
        $formData	= $request->all();
        $detail 	= new \stdClass();
        $response	= array();
        $validator = Validator::make(
            $request->all(),
            array(
                'phone' 			=> 'required',
                'otp'			=> 'required'

            )
        );
        if ($validator->fails()) {

            $allErrors = [];
            foreach ($validator->errors()->all() as $message) {
                $allErrors[] = $message;
            }

            $response = array(
                'status' => 0,
                'message' => $allErrors,

            );
        }
else
{
    $userInfo = Customer::where('phone',$request->phone)->first();
    $detail = '' ;
    if(!empty($userInfo)){
        if($userInfo->is_verified == 1){
            $success = 0;
            $errors	= 'تم تفعيل الحساب من قبل';

        }else{

             if((string)$userInfo->otp ==  $request->otp){

                $userInfo->update(['is_verified' => 1]);

                $detail = $userInfo ;

                $success = 1;
                $errors	= 'تسجيل المستخدم بنجاح ، يرجى تسجيل الدخول';
            }else{
                $success = 0;
                $errors	= 'رمز التحقق خاطئ';
            }
        }
    }else{
        $success = 0;
        $errors	= 'لا يوجد حساب ';
    }


    $response	=	array(
        'status' 	=>$success,
        'message'	=> $errors,
        'detail'	=> $detail,

    );
}
        return  ($response);

    }

    public static function customerLogin(Request $request)
    {
        $formData	= $request->all();
        $detail = (object) null;
        $validator = Validator::make(
            $request->all(),
            array(
                'phone' 			=> 'required',
                'password'			=> 'required|min:6',
                'device_id'			=> 'required',
                'device_type'		=> 'required',

            )
        );
        if ($validator->fails())
        {
            $allErrors =  [];
            foreach ($validator->errors()->all() as $message){
                $allErrors[] =  $message;
            }

            $response	=	array(
                'status' 	=> 0,
                'message'	=> $allErrors,
                'detail'    => ''
            );

        }
        else
        {
            $response	=	array(
                'status' 	=> 0,
                'message'	=> 'خطا في رقم الهاتف او كلمة المرور',


            );
            $userdata = array(
                'phone' 					=> $request->phone,
                'password' 					=> $request->password,
                //	'user_role_id' 				=> 2,
            );
              if (Auth::guard('customers')->attempt($userdata)) {

                 $user = Auth::guard('customers')->user();
                  $user->device_token=$request->device_token;
                  $user->device_id=$request->device_id;
                  $user->device_type=$request->device_type;
                      $user->save();
                 if(!$user->is_verified)
                 {
                     $response	=	array(
                         'status' 	=> 0,
                         'message'	=> "We have sent a OTP on your phone no. Please verify your phone no.",


                     );
                 }
                 else
                 {
                     $event = Booking::with('ceremony','ceremonywithdescription')->
                         whereHas('ceremony', function($q){
                         $q->where('date', '>=', \Carbon\Carbon::today()->format('Y-m-d'));
                     })->where('user_id',$user->id)->orderBy('id','DESC')->first();
                     $response	=	array(
                         'status' 	=> 1,
                         'message'	=> 'تم تسجيل الدخول',
                         'user'    => new CustomerResources($user),
                         'booking'   =>$event ,
                         'token'=>  $user->createToken('token')->accessToken
                     );
                 }


            }

            }
        return  ($response);

    }
    public static function customerProfile(Request $request)
    {
        $response	=	array(
            'status' 	=> 1,
            'message'	=> 'تفاصيل الحساب',
            'user'    => new CustomerResources(Auth::guard('customers_api')->user()),
        );
        return $response;
     }
}