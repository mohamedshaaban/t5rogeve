<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeviceInfo;
use Backpack\NewsCRUD\app\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
class RegisterController extends Controller
{
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
            $customer->faulty			=  $request->faulty;
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
            $password = "A55656637a";
            $sender = "ACADEMY";
            $lang = "2";
            $digits = 4;
            //$code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $to = "+965".$request->phone;

            $userInfo = Customer::where('phone',$request->phone)->first();
            $otp = $userInfo->otp;
            $text = "Your OTP is ".$otp;

            $status = "";
            $query = "username=".$username."&password=".$password."&sender=".$sender."&mobile=".$to."&lang=".$lang."&message=".$text;
            $Curl_Session = curl_init($url_address);
            curl_setopt ($Curl_Session, CURLOPT_POST, 1);
            curl_setopt ($Curl_Session, CURLOPT_POSTFIELDS, $query);
            curl_setopt ($Curl_Session, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($Curl_Session, CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec ($Curl_Session);

             curl_close ($Curl_Session);

             $response	=	array(
                'status' 	=> 0,
                'message'	=> 'تم انشاء الحساب',
                'detail'    => $customer
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
                 if(!$user->is_verified)
                 {
                     $response	=	array(
                         'status' 	=> 0,
                         'message'	=> "We have sent a OTP on your phone no. Please verify your phone no.",


                     );
                 }
                 else
                 {
                     $response	=	array(
                         'status' 	=> 1,
                         'message'	=> 'تم تسجيل الدخول',
                         'user'    => $user,
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
            'status' 	=> 0,
            'message'	=> 'تفاصيل الحساب',
            'user'    => Auth::guard('customers')->user(),
        );
        return $response;
     }
}