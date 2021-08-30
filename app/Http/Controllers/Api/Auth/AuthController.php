<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\Customer;
use App\Models\DeviceInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth, Response, DB, Hash, Session, Redirect, Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function userUpdatePassword(Request $request)
    {
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(

            'password' 		    => "Please Enter Password",
        );

        $validator = Validator::make(
            $request->all(),
            array(

                'password'			=> 'required',
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
        else
        {

            $password =  Hash::make($formData['password']);
            $user_id = Auth::guard('customers_api')->user();
            $user_id = $user_id->id;

            $results = User::where('id',$user_id)->update(['password' => $password]);

                if($results){
                    $response	=	array(
                        'status' 	=> 1,
                        'message'	=> "Password Updated Successfully",

                    );
                }else{
                    $response	=	array(
                        'status' 	=> 0,
                        'message'	=> 'Unsuccessful',
                    );
                }


        }

        return Response::json($response);
    }
    public function userUpdateProfile(Request $request)
    {
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(
            'full_name.required' 	=> "Please Enter Name",
            'father_name.required' 	=> "Please Enter Father Name",
            'grandfather_name.required' 	=> "Please Enter GrandFather Name",
            'gender.required' 		=> "Please Enter Gender",
            'faulty.required' 		=> "Please Enter Faulty",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'full_name' 		=> 'required',
                'father_name'       => 'required',
                'grandfather_name'  => 'required',
                'gender' 			=> 'required',

                'faulty'			=> 'required',

            ), $messages
        );

        /// 111
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

            $user_id = Auth::guard('customers_api')->user();
            $user_id = $user_id->id;

                $full_name = $formData['full_name'];
                $father_name = $formData['father_name'];
                $grandfather_name = $formData['grandfather_name'];
                $family_name = $formData['family_name'];
                $gender = $formData['gender'];
                 $civil_id = $formData['civil_id'];
                $faulty = $formData['faulty'];
                 $imageArr = array();


                 if($request->hasFile('image')){

                    $results = Customer::where('id',$user_id)->update([
                        'full_name' => $full_name,
                        'civil_id' => $civil_id,
                        'faulty' => $faulty,
                        'gender' => $gender,
                        // 'image' => $filename ,
                        'father_name' => $father_name,
                        'family_name' => $family_name,
                        'grandfather_name' => $grandfather_name

                    ]);

                    $data = Customer::where('id',$user_id)->select('id','full_name','grandfather_name','father_name','gender','family_name','civil_id','faulty','phone','image')
                        ->first();

                    $image       = $request->file('image');
                    $extension 	=	 $image ->getClientOriginalExtension();
                    $fileName	=	time().'-image.'.$extension;

                    if($image->move('uploads/', $fileName)){
                         $affected = Customer::where('id',$user_id)
                            ->update([
                                'image'	 =>  	$fileName,
                            ]);
                    }

                    if($results){
                        $response	=	array(
                            'status' 	=> 1,
                            'message'	=> "Profile Updated Successfully",
                            'detail'   => $data,

                        );
                    }else{
                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'Unsuccessful',
                            'detail'	=> $detail
                        );
                    }

                    return Response::json($response); die;


                }else{
                    $results = Customer::where('id',$user_id)->update(['full_name' => $full_name,'civil_id' => $civil_id, 'faulty' => $faulty,'gender' => $gender,'father_name' => $father_name,'family_name' => $family_name,'grandfather_name' => $grandfather_name]);

                    $data = Customer::where('id',$user_id)->select('id','full_name','grandfather_name','father_name','gender','family_name','civil_id','faulty','phone','image')
                        ->first();

                    if($results){
                        $response	=	array(
                            'status' 	=> 1,
                            'message'	=> "Profile Updated Successfully",
                            'detail'   => $data,

                        );
                    }else{
                        $response	=	array(
                            'status' 	=> 0,
                            'message'	=> 'Unsuccessful',
                            'detail'	=> $detail
                        );
                    }

                    return Response::json($response); die;
                }

        }/// end 111


    }

    public function userUpdatePhone(Request $request)
    {
        $formData = $request->all();
        $detail = new \stdClass();
        $response = array();
        $messages = array(
            'phone.required' => "Please Enter Mobile No.",
            'otp.required' => "Please Enter OTP",
        );
        $validator = Validator::make(
            $request->all(),
            array(
                'phone' => 'required',
                'otp' => 'required|min:4|max:5',
            ), $messages
        );
        if ($validator->fails()) {

            $allErrors = '';
            foreach ($validator->errors()->all() as $message) {
                $allErrors = $message;
                break;
            }
            $response = array(
                'status' => 0,
                'message' => $allErrors,
            );
        } else {
            $user_id = Auth::guard('customers_api')->user();
            $phone = $formData['phone'];
            $user = Auth::guard('customers_api')->user();
            if ($user->otp == $request->otp) {
                $results = $user->update(['phone' => $phone]);
                $data = $user;
                if ($results) {
                    $response = array(
                        'status' => 1,
                        'message' => "تم تعديل الرقم بنجاح",
                        'detail' => $data,
                    );
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Unsuccessful',
                        'detail' => $data
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'رمز التحقق خاطئ'
                );
            }
            return ($response);
        }
    }

    public function userResetPassword(Request $request)
    {
        $formData = $request->all();
        $detail = (object)null;

        $response = array();
        $messages = array(
            'otp' => "Please Enter otp",
            'password' => "Please Enter Password",
            'phone' => "Please Enter Phone"
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'otp' => 'required',
                'password' => 'required',
                'phone' => 'required'

            ), $messages
        );
        if ($validator->fails()) {
            $allErrors = '';
            foreach ($validator->errors()->all() as $message) {
                $allErrors = $message;
                break;
            }
            $response = array(
                'status' => 0,
                'message' => $allErrors,
                'detail' => $detail
            );
        } else {
            $otp = $formData['otp'];
            $password = Hash::make($formData['password']);
            $phone = $formData['phone'];
            //$confirm_password = $formData['confirm_password'];

            $results = Customer::where('phone', $phone)->update(['password' => $password]);

            if ($results) {
                $response = array(
                    'status' => 1,
                    'message' => "Password Changed Successfully",

                );
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'Unsuccessful',
                );
            }
        }

        return ($response);
    }


    public function forgetPasswordOtpVerify(Request $request)
    {
        $formData = $request->all();
        $detail = new \stdClass();

        $response = array();
        $messages = array(
            'phone.required' => "Please Enter Mobile No.",
            'otp.required' => "Please Enter OTP",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'phone' => 'required',
                'otp' => 'required|min:4|max:5',
            ), $messages
        );
        if ($validator->fails()) {

            $allErrors = '';
            foreach ($validator->errors()->all() as $message) {
                $allErrors = $message;
                break;
            }

            $response = array(
                'status' => 0,
                'message' => $allErrors,

            );

        } else {

            $userInfo = Customer::where('phone', $request->phone)->first();
            if (!empty($userInfo)) {

                if ($userInfo->otp == $request->otp) {

                    Customer::where('id', $userInfo->id)
                        ->update(['is_verified' => 1]);


                    $detail = Customer::where('id', $userInfo->id)->select('id', 'email', 'full_name', 'civil_id', 'faulty')
                        ->first();


                    $success = 1;
                    $errors = 'Please Reset Password';
                } else {
                    $success = 0;
                    $errors = 'Incorrect OTP.';
                }

            } else {
                $success = 0;
                $errors = 'No Account is found with Phone.';
            }


            $response = array(
                'status' => $success,
                'message' => $errors,

            );

        }

        return ($response);

    }

    public function userForgetPassword(Request $request)
    {

        $formData = $request->all();
        $detail = (object)null;

        $response = array();
        $userInfo = Customer::where('phone', $request->phone)->first();
        if (!empty($userInfo)) {

            $phone = $formData['phone'];


            $otp = mt_rand(1000, 9999);
            $results = $userInfo->update(['otp' => $otp]);


            if ($results) {
                $response = array(
                    'status' => 1,
                    'message' => "OTP Sent Successfully",
                    /*'detail'   => $data,*/

                );
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'Unsuccessful',
                    /*'detail'	=> $detail*/
                );
            }


        } else {
            $response = array(
                'status' => 0,
                'message' => 'Entered phone no not registered, Please enter a registered phone no.',
                /*'detail'	=> $detail*/
            );

            return Response::json($response);

        }

        $url_address = "http://www.kwtsms.com/API/send/?";
        $username = "academy";
        $password = "A55656637a";
        $sender = "ACADEMY";
        $lang = "2";
        $digits = 4;
        //$code = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $to = "+965" . $request->phone;
        $userInfo = Customer::where('phone', $request->phone)->first();
        $otp = $userInfo->otp;
        $text = "Your OTP is " . $otp;

        $status = "";
        $query = "username=" . $username . "&password=" . $password . "&sender=" . $sender . "&mobile=" . $to . "&lang=" . $lang . "&message=" . $text;
        $Curl_Session = curl_init($url_address);
        curl_setopt($Curl_Session, CURLOPT_POST, 1);
        curl_setopt($Curl_Session, CURLOPT_POSTFIELDS, $query);
        curl_setopt($Curl_Session, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($Curl_Session, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($Curl_Session);

        /* Print_r($result);
         die;*/
        curl_close($Curl_Session);
        return ($response);

    }

    public function updateDeviceToken(Request $request)
    {
        $formData	= $request->all();
        $detail = (object) null;

        $response	= array();
        $messages = array(
            'device_token' 		    => "Please Enter Device Token",
        );

        $validator = Validator::make(
            $request->all(),
            array(
                'device_token' 			=> 'required',
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
                'detail'    => $detail
            );
        }
        else {
            $user_id = Auth::guard('customers_api')->user();
            $user_id = $user_id->id;
            $device_token = $formData['device_token'];
            $device_info = array(
                'device_token' => $request->device_token,
            );

            $results = DeviceInfo::where('user_id', $user_id)->update($device_info);


            if ($results) {
                $response = array(
                    'status' => 1,
                    'message' => "Device Token Updated Successfully",
                    'detail' => $detail,

                );
            } else {
                $data = DeviceInfo::create(['user_id'=> $user_id,
                    'device_token' => $request->device_token
                ]);

                $response = array(
                    'status' => 0,
                    'message' => "Device Token Created Successfully",
                    'detail' => $detail
                );
            }

        }


        return ($response);
    }


    public function checkPhone(Request $request)
    {

        $formData = $request->all();
        $detail = (object)null;

        $response = array();
        $messages = array(
            'phone.required' => "Please Enter Mobile No."

        );

        $validator = Validator::make(
            $request->all(),
            array(
                'phone' => 'required|unique:customers'
            ), $messages
        );

        /// 111
        if ($validator->fails()) {
            $allErrors = '';
            foreach ($validator->errors()->all() as $message) {
                $allErrors[] = $message;
                break;
            }

            $response = array(
                'status' => 0,
                'message' => $allErrors,
                'detail' => $detail
            );
        }
        $userInfo = Customer::where('phone', $request->phone)->first();
        if (!empty($userInfo)) {


            $response = array(
                'status' => 0,
                'message' => 'رقم الهاتف مسجل مسبقا',

            );
            return ($response);


        } else {

            $user_id = Auth::guard('customers_api')->user();
            $user_id=$user_id->id;
            $phone = $formData['phone'];

            $otp = mt_rand(1000, 9999);

            Customer::where('id', $user_id)->update(['otp' => $otp]);

            $url_address = "http://www.kwtsms.com/API/send/?";
            $username = "academy";
            $password = "A55656637a";
            $sender = "ACADEMY";
            $lang = "2";
            $digits = 4;
            //$code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $to = "+965" . $request->phone;

            $text = "Your OTP is " . $otp;

            $status = "";
            $query = "username=" . $username . "&password=" . $password . "&sender=" . $sender . "&mobile=" . $to . "&lang=" . $lang . "&message=" . $text;
            $Curl_Session = curl_init($url_address);
            curl_setopt($Curl_Session, CURLOPT_POST, 1);
            curl_setopt($Curl_Session, CURLOPT_POSTFIELDS, $query);
            curl_setopt($Curl_Session, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($Curl_Session, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($Curl_Session);

            //Print($result);
            curl_close($Curl_Session);


            $response = array(
                'status' => 1,
                'message' => 'يرجى إدخال رمز التحقق الذي تلقيته على رقم الموبايل',
                'detail' => $detail
            );

            return ($response);

        }/// end 111
    }

    public function userLogout(Request $request)
    {


        $user = Auth::guard('customers_api')->user();
        $user_id = $user->id;
                $results = Customer::find($user_id)->update(['is_login' => 0, 'is_temp_login' => 0]);
                if($results)
                {
                    $response	=	array(
                        'status' 	=> 1,
                        'detail'	=> '',
                        'message'   => "Logout successfully"
                    );
                }
                else
                {
                    $response	=	array(
                        'status' 	=> 0,
                        'detail'	=> '',
                        'message'	=> 'Unsuccessfull'
                    );
                }
        return  $response;
    }

}