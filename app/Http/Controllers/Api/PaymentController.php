<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\PaymentLog;
use App\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Auth,Response,DB,Hash,Session,Redirect,Validator;
use Asciisd\Knet\KPayManager;
use Asciisd\Knet\Payment;
use Asciisd\Knet\KnetTransaction;
use IZaL\Knet\KnetBilling;

class PaymentController extends Controller
{
    
    public function GetTransactionByUserId(Request $request)	
	{
		
		$response	= array();
		$messages = array(
			'limit'                     => "Please Enter Limit",
			'page'                      => "Please Enter Page No",
			   );
			   
		   $validator = Validator::make(
				   $request->all(),
				   array(
					   'limit'          => 'required|numeric|min:0|not_in:0',
					   'page'           => 'required|numeric|min:0|not_in:0', 
				   ), $messages
			   );
			   
			if ($validator->fails()) 
			{    
			   $response	=	array(
				'status' 	=> 0,
				'message'	=> $validator->messages(),
				//'detail'    => $detail
			);
		   
		   }else{

			   $page          =  $request->page;
			   $limit         =  $request->limit;
			   $language_id         =  1;
                $user = Auth::guard('customers_api')->user();
                $user_id = $user->id;
              $p_no   =  $page - 1;
			  $total  =  $limit * $p_no;
			   


                 $transaction_detail= DB::table('payment_log as pl')
						 ->join('users as u', 'pl.user_id', '=', 'u.id')
						 ->Leftjoin('ceremony as c', 'c.id', '=', 'pl.event_id')
           				 ->where('pl.user_id',$user_id)
						 ->select('pl.*','u.full_name','c.name as event_name')
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
			 
				   

			}
		   return Response::json($response); die;

	}

	public function EventEnrollAmt(Request $request)	
	{
		$formData	= $request->all();
		$response	= array();
		$messages = array(
		'event_id.required' 	 => "Please Enter Event Id.",
		'enroll_amt.required' 	 => "Please Enter Enroll Amount.",
		'payment_id.required'	 => 'Please Enter Payment Id'
		);

		$validator = Validator::make(
			$request->all(),
			array(
				'event_id' 		=> 'required',
				'enroll_amt' 	=> 'required',
				'payment_id' 	=> 'required',
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
				$event_id    = $request->event_id;
				$user_id    = $request->user_id;
				$enroll_amt    = $request->enroll_amt;
				$session_token    = $request->session_token;
				$payment_id 	= $request->trans_id;
				$date       = date("Y-m-d H:i:s");
				

				$payment_details=PaymentLog::whereId($payment_id)->first();
				dd($payment_details);
				if(empty($payment_details) || $payment_details->result!='CAPTURED')
				{
					$response	=	array(
					'status' 	=> 0,
					'message'	=> 'فشل الاجراء',
					);
				}
				else
				{
								$obj 					=  new Booking();
								$obj->user_id			=  $user_id;					
								$obj->event_id 		    =  $event_id;
								$obj->enroll_amt 		=  $enroll_amt;
								$obj->session_token		=  $session_token;
								$obj->payment_type		=  "Enroll";
								$obj->save();
								$bookingId	=	$obj->id;	

								$response	=	array(
									'status' 	=> 1,
									'message'	=> "Enroll Amount has been saved successfully",
									'transationid'=>$payment_details->tranid,
									'paymentID'=>$payment_details->paymentid,
									'amount'=>$payment_details->amt,
									'created_at'=>$payment_details->created_at,
								
								);
							}
								
				}
					
		
	return Response::json($response); die;

	}


	public function EnrollmentAmtpaymentGateway(Request $request)	
	{    
			$formData=$request->all();	
			$response=array();
			$messages=array(
						'event_id.required'			=> 'Please Enter Event ID',
						'enroll_amt.required'		=> 'Please Enter Price',

			);

			$validator=validator::make(
						$request->all(),
						array(
							'enroll_amt'   => 'required',
							'event_id'		=> 'required',

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
			}
			else
			{
				$price =  $formData['enroll_amt'];
				$eventid =  $formData['event_id'];
                $user = Auth::guard('customers_api')->user();
                $session_token = mt_rand();
                $user_id = $user->id;
				$user_details=Customer::where('id',$user_id)->first();
				$user_email=$user_details->email;
				//payment method start
				$TranAmount=$price;
				$TranTrackid=strtotime("now");
				$TranportalId="109301";
				$ReqTranportalId="id=".$TranportalId;
				$ReqTranportalPassword="password=109301pg";
				$ReqAmount="amt=".$TranAmount;
				$ReqTrackId="trackid=".$TranTrackid;
				$ReqCurrency="currencycode=414";
				$ReqLangid="langid=USA";
				$ReqAction="action=1";
				$udf1="udf1=".$user_email;
				$udf2="udf2=".$user_id;
				$udf3="udf3=".$eventid;
				$ResponseUrl=url('/paymentGatewayipn');


				$ReqResponseUrl="responseURL=".$ResponseUrl;

				$ErrorUrl=url('/result');

				$ReqErrorUrl="errorURL=".$ErrorUrl;
				$param=$ReqTranportalId."&".$ReqTranportalPassword."&".$ReqAction."&".$ReqLangid."&".$ReqCurrency."&".$ReqAmount."&".$ReqResponseUrl."&".$ReqErrorUrl."&".$ReqTrackId."&".$user_id."&".$session_token."&".$udf1."&".$udf2."&".$udf3;

				$termResourceKey="7Y1B91E78XMK5MT9";

				$param=$this->encryptAES($param,$termResourceKey)."&tranportalId=".$TranportalId."&errorURL=".$ErrorUrl;
				
			    $ch = curl_init();                    // Initiate cURL
			    $url = "https://kpaytest.com.kw/kpg/PaymentHTTP.htm?param=paymentInit"; // Where you want to post data
			    curl_setopt($ch, CURLOPT_URL,$url);
			    curl_setopt($ch, CURLOPT_POST, true);  // Tell cURL you want to post something
			    curl_setopt($ch, CURLOPT_POSTFIELDS, "trandata=".$param); // Define what you want to post
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the output in string format
				$output = curl_exec ($ch); // Execute
			 	$intext = strpos($output, 'Error Code') !== false; // note !==, not !=
				if($intext==1){
					$response =array(
						'status'=> 0,
						'message'=> 'Payment gate api issue'
						);	
					return Response::json($response);die;
				} 
				//echo $intext ? 'Online' : 'Offline';

			    curl_close ($ch); // Close cURL handle
				
			    $regex=	"<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
				$matches = array(); //create array
				$pattern = "/$regex/";

				preg_match_all($pattern, $output, $matches); 

				$suv_link=$matches[0][0];

				preg_match('/href=(["\'])([^\1]*)\1/i', $suv_link, $m);
				//preg_match_all('!\d+!', $m[2], $paymentID);
				$url_link=$m[2];

				$url_values=explode("&", $url_link);
				
				preg_match_all('!\d+!', $url_values[0], $paymentID);
				
				if($paymentID[0]!=null)
				{
					/*DB::table('payment_log')->insert(['userid' => $user_id,'eventid'=>$eventid,'price' => $price,'quantity' => $quantity,'total' => $TranAmount,'paymentid' => $paymentID[0][0],'status' =>'pending']);*/

					$response =array(
					'status'=> 1,
					'paymentID'=> $paymentID[0][0],
					'paymentlink'=> $url_link
					
					);	

				}
				else
				{
					$response =array(
					'status'=> 0,
					'Message'=> $url_values[1]
					);	
				}
				
				return Response::json($response);die;
				
			}
	}

	public function paymentlog(Request $request)
	{
			$formData=$request->all();	
			$response=array();
			$messages=array(
						'event_id.required'			=> 'Please Enter Event ID',
						'tran_id.required'			=> 'Please Enter TranID',
						'marchant_id.required'		=> 'Please Enter marchantId',
						'ref.required'				=> 'Please Enter Ref',
						'track_id.required'			=> 'Please Enter TrackID',
						'card_type.required'		=> 'Please Enter card_type',
						'amount.required'			=> 'Please Enter amount',
						'invoic_id.required'		=> 'Please Enter invoicID',
						'phone.required'			=> 'Please Enter phone',
						'status.required'			=> 'Please Enter status'
							
			);

			$validator=validator::make(
						$request->all(),
						array(

							'event_id'		=> 'required',
							'tran_id'		=> 'required',
							'marchant_id'	=> 'required',
							'ref'			=> 'required',
							'track_id'		=> 'required',
							'card_type'		=> 'required',
							'amount'		=> 'required',
							'invoic_id'		=> 'required',
							'phone'			=> 'required',
							'status'		=> 'required'
										
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
 			}
			else
			{

				$event_id =  $formData['event_id'];
				$tran_id =  $formData['tran_id'];
				$marchant_id =  $formData['marchant_id'];
				$ref =  $formData['ref'];
				$track_id =  $formData['track_id'];
				$card_type =  $formData['card_type'];
				$amount =  $formData['amount'];
				$invoic_id =  $formData['invoic_id'];
				$phone =  $formData['phone'];
				$status =  $formData['status'];

				if($status=="fail")
				{
					$status="NOT+CAPTURED";	
				}
				else
				{
					$status="CAPTURED";
				}
                $user = Auth::guard('customers_api')->user();
                $user_id = $user->id;
				$paymenlog=new Invoice();

				$paymenlog->user_id=$user_id;
				$paymenlog->event_id=$event_id;
				$paymenlog->tranid=$tran_id;
				$paymenlog->marchant_id=$marchant_id;
				$paymenlog->ref=$ref;
				$paymenlog->trackid=$track_id;
				$paymenlog->card_type=$card_type;
				$paymenlog->amt=$amount;
				$paymenlog->invoic_id=$invoic_id;
				$paymenlog->phone=$phone;
				$paymenlog->result=$status;

				$paymenlog->save();

				$response =array(
				'status'=> 1,
				'message'=> "success"
				);	
				
				return Response::json($response);die;
				
			}
	}
    	///////
	 function payknet(Request $request) {
	            


         $user = Auth::guard('customers')->user();

  	     $amount  = $request->amount ;

	          $event_id  = $request->event_id ;


             //Knet Documentation
             $TranAmount = number_format((float)$amount, 2, '.', '');
             $TranportalId=config('app.KENT_TRANSPORT_ID');
             $ReqTranportalId="id=".$TranportalId;
             $TranportalPassword=config('app.KENT_TRANSPORT_PASSWORD');
             $ReqTranportalPassword="password=".$TranportalPassword;
             $ReqAmount="amt=".$TranAmount;
             $TranTrackid=$this->generateRandomString(10);
             $ReqTrackId="trackid=".$TranTrackid;
             $ReqCurrency="currencycode=414";
             $ReqLangid="langid=USA";
             $ReqAction="action=1";
             $ResponseUrl=route('knetsuccess');
             $ReqResponseUrl="responseURL=".$ResponseUrl;
             $ErrorUrl=route('kneterror');
             $ReqErrorUrl="errorURL=".$ErrorUrl;
             $ReqUdf1="udf1=".$request->first_name;
             $ReqUdf2="udf2=".$request->family;
             $ReqUdf3="udf3=".$request->phone;
             $ReqUdf4="udf4=".$request->user_id;
             $ReqUdf5="udf5=".$event_id;
             $param=$ReqTranportalId."&".$ReqTranportalPassword."&".$ReqAction."&".$ReqLangid."&".$ReqCurrency."&".$ReqAmount."&".$ReqResponseUrl."&".$ReqErrorUrl."&".$ReqTrackId."&".$ReqUdf1."&".$ReqUdf2."&".$ReqUdf3."&".$ReqUdf4."&".$ReqUdf5;




             $termResourceKey=config('app.KENT_RESOURCE_KEY');
             $param=$this->encryptAES($param,$termResourceKey)."&tranportalId=".$TranportalId."&responseURL=".$ResponseUrl."&errorURL=".$ErrorUrl;
             $param = "https://kpaytest.com.kw/kpg/PaymentHTTP.htm?param=paymentInit"."&trandata=".$param;

 $response =array(
				'status'=> 1,
				'paymenturl'=> $param
				);	
				
				return Response::json($response);die; 

		
	 }

	 public function knetsuccess(Request $request)
     {
         $DecrptedData = ($this->decrypt($request->trandata,config('app.KENT_RESOURCE_KEY')));
         parse_str($DecrptedData, $get_array);

         $paymentlog = PaymentLog::create([
             'user_id'=>$get_array['udf4'],
             'event_id'=>$get_array['udf5'],
//             'booking_no'=>$get_array[''],
             'paymentid'=>$get_array['paymentid'],
             'result'=>$get_array['result'],
             'auth'=>$get_array['auth'],
             'avr'=>$get_array['avr'],
             'ref'=>$get_array['ref'],
             'tranid'=>$get_array['tranid'],
             'postdate'=>$get_array['postdate'],
             'trackid'=>$get_array['trackid'],
             'amt'=>$get_array['amt'],
             'authRespCode'=>$get_array['authRespCode'],
//             'marchant_id'=>$get_array[''],
             'card_type'=>'knet',
//             'invoic_id'=>$get_array[''],
             'phone'=>$get_array['udf3']
         ]);
         return redirect(route('getKnetsuccess',['trans_id'=>$paymentlog->id]));

     }
     
	 public function getknetsuccess(Request $request)
     {
      return PaymentLog::find($request->trans_id);
     }
	 public function kneterror(Request $request)
     {

     }
    function decrypt($code,$key) {

        $code = $this->hex2ByteArray(trim($code));
        $code=$this->byteArray2String($code);
        $iv = $key;
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
        return $this->pkcs5_unpad($decrypted);
    }

    function byteArray2String($byteArray) {
        $chars = array_map("chr", $byteArray);
        return join($chars);
    }

    function pkcs5_unpad($text) {
        $pad = ord($text[strlen($text)-1]);
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    function hex2ByteArray($hexString) {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }
    //AES Encryption Method Starts
    function encryptAES($str,$key) {
        $str = $this->pkcs5_pad($str);
        $encrypted = openssl_encrypt($str, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $key);
        $encrypted = base64_decode($encrypted);
        $encrypted=unpack('C*', ($encrypted));
        $encrypted=$this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function pkcs5_pad ($text) {
        $blocksize = 16;
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    function byteArray2Hex($byteArray) {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }
	 	 function payknetremining(Request $request) {
	     
            ///
             
         	$userdata = array(
						'phone' 					=> $request->phone,
						'password' 					=> $request->password,
		);
		
			if (Auth::attempt($userdata)) {
             $user = Auth::user();
  	     $amount  = $request->amount ;

	          $event_id  = $request->event_id ;
	          
	          
    $knet = KPayManager::make($amount, [
        'user_id' => $request->user_id, 
        'udf1' => $request->first_name,
        'udf2' => $request->family,
        'udf3' => $request->phone,
        'udf4' => $request->user_id,
        'udf5' => $event_id
    ]);
// make KnetTransaction record in your database
$payment = new Payment(
            KnetTransaction::create($knet->toArray())
        );
        

$payment->actionUrl(); // redirect user to pay with url generated
 $response =array(
				'status'=> 1,
				'paymenturl'=> $payment->actionUrl()
				);	
				
				return Response::json($response);die; 
 }else{
                $response =array(
				'status'=> 0,
				'message'=> 'لم يتم تسجيل الدخول'
				);	
				return Response::json($response);die; 
            }
		
	 }
	 
	 ////
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