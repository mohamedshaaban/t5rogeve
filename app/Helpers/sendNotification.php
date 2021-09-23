<?php

use App\Models\DeviceInfo;
use App\Models\UserNotifications;
use App\Models\GuestsToken;
use App\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

if ( ! function_exists( 'sendNotification' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function sendNotification( $user_id = null , $link, $text, $module) {
        if(!$user_id)
        {
            $users = User::all();
            foreach($users as $user )
            {
                $data = ['link'=>$link,
                    'text'=>$text,
                    'module'=>$module,];

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);

                $notificationBuilder = new PayloadNotificationBuilder('Academy - ');
                $notificationBuilder->setBody($text)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['a_data' => $data]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = $user->device_token;
                if($token) {


                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
                    $downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
                    $downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
                    $downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
                    $downstreamResponse->tokensWithError();
                }
            }
            $data = ['link'=>$link,
                'text'=>$text,
                'module'=>$module,];
            // You must change it to get your tokens
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            $notificationBuilder = new PayloadNotificationBuilder('Minimi - ');
            $notificationBuilder->setBody($text)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = User::get()->pluck('device_token')->toArray();

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
            $downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
            $downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
            $downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
            $downstreamResponse->tokensWithError();
        }
        else {
            $title = 'Academy - ';
             $tokens = DeviceInfo::where('user_id',$user_id)->pluck('device_token')->toArray();
             $data = ['link' => $link,
                'text' => $text,
                'module' => $module,];

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($text)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();


            if ($tokens) {
                 $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
                $downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
                $downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
                $downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
                $downstreamResponse->tokensWithError();

            }
        }
    }
}
