<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NotificationRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Booking;
use App\Models\Ceremony;
use App\Models\Customer;
use App\Models\DeviceInfo;
use App\Models\Notification;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use http\Env\Request;
use Illuminate\Support\Facades\App;

class NotificationsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Notification::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/notifications');
        CRUD::setEntityNameStrings(trans('admin.notifications'), trans('admin.notifications'));
    }

    protected function setupListOperation()
    {
        $this->crud->removeButton('update');
        CRUD::addColumn([
            'name'           => 'notification',
            'type'           => 'text',
            'label'          => trans('admin.notification'),
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);

        CRUD::addColumn([
            'name'           => 'ceremony', // the column that contains the ID of that connected entity;

            'attribute'        =>'name',
            'label'          => trans('admin.Ceremony For'),
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'name'           => 'link',
            'type'           => 'text',
            'label'          => trans('admin.link')
        ]);

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([
            'name'           => 'ceremony', // the column that contains the ID of that connected entity;

            'attribute'        =>'name',
            'label'          => trans('admin.Ceremony For'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'default'=>'2',
            'inline'      => true,
            'attributes' => [
                'class'       => 'form-control notificationceremonyfor-class'],
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "Female",
                1 => "Male",
                2 => "Both"
            ],
        ]);


        CRUD::addField([
            'name'           => 'sent_to', // the column that contains the ID of that connected entity;

            'attribute'        =>'sent_to',
            'label'          => trans('admin.Sent To'),
            'type'  => 'select_from_array',
            'tab'   => 'Texts',
            'allows_null' => false,
            'default'=>0,
            'inline'=>true,
            'attributes' => [
            'class'       => 'form-control notificationfor-class'],
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "All",
                1 => "Events",
                2 => "User"
            ],

        ]);


        CRUD::addField(
            [    // Select2Multiple = n-n relationship (with pivot table)
                'label'     => trans('admin.Event'),
                'type'      => 'select2_multiple',
                'name'      => 'events', // the method that defines the relationship in your Model
                'tab'   => 'Texts',
                'entity'    => 'events', // the method that defines the relationship in your Model
                'model'     => "App\Models\ceremony", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                 'select_all' => true, // show Select All and Clear buttons?
                'attributes' => [
                    'style' =>'display:none',
                    'class'       => 'form-control notificationevent-class'],
                // optional
                "visibility" => [
                    'field_name' => 'sent_to',
                    'value'      => 1,
                    'add_disabled' => true, // if you need to disable this field value to not be send through create/update request set it to true, otherwise set it to true
                ],
                'options'   => (function ($query) {
                    if(backpack_user()->faculty_id!=0)
                    {
                        return $query->orderBy('name', 'ASC')->where('faculty',backpack_user()->faculty_id)->get();
                    }
                    else
                    {
                        return $query->orderBy('name', 'ASC')->get();
                    }
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ]);


        CRUD::addField([  // Select2
            'label' => trans('admin.Student'),
            'type' => 'select2',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'user', // the method that defines the relationship in your Model
             'tab' => 'Texts',
            // optional
            "visibility" => [
                'field_name' => 'sent_to',
                'value'      => '2',
                'add_disabled' => true, // if you need to disable this field value to not be send through create/update request set it to true, otherwise set it to true
            ],
             'model'     => Customer::class, // foreign key model
            'attribute' => 'all_name', // foreign key attribute that is shown to user
            'attributes' => [
                'style' =>'display:none',
                'class'       => 'form-control notificationuser-class'],
              'options'   => (function ($query) {
                return $query->orderBy('phone', 'ASC')->get();
            }),
        ]);
        CRUD::addField([
            'name'           => 'full_name',
            'type'           => 'text',
            'label'          => trans('admin.First name'),
            'tab' => 'Texts',
            'attributes' => [
                
                'class'       => 'form-control notificationfull_name-class'],
        ]);
        CRUD::addField([
            'name'           => 'grandfather_name',
            'type'           => 'text',
            'label'          => trans('admin.Second name'),
            'tab' => 'Texts',
            'attributes' => [
                'class'       => 'form-control notificationgrandfather_name'],
        ]);
        CRUD::addField([
            'name'           => 'father_name',
            'type'           => 'text',
            'label'          => trans('admin.Third name'),
            'tab' => 'Texts',
            'attributes' => [
                'class'       => 'form-control notificationfather_name'],
        ]);
        CRUD::addField([
            'name'           => 'family_name',
            'type'           => 'text',
            'label'          => trans('admin.Forth name'),
            'tab' => 'Texts',
            'attributes' => [
        'class'       => 'form-control notificationfamily_name-class'],
        ]);

        CRUD::addField([
            'name'           => 'notification',
            'type'           => 'ckeditor',
            'label'          => trans('admin.notification'),
            'tab' => 'Texts'
        ]);

        CRUD::addField([
            'name'           => 'link',
            'type'           => 'text',
            'label'          => trans('admin.link'),
            'tab' => 'Texts'

        ]);

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function store(\Illuminate\Http\Request  $request)
    {
        $this->crud->unsetValidation(); // validation has already been run
        $form = backpack_form_input();
//        dd($request->all());
         $response = $this->traitStore();
        $for = $request->sent_to;
         if ($for == 0){
             $notify  = Notification::find($this->crud->entry->id);
             foreach($notify->events as $event)
             {
                 $usersids = [] ;
                 $notification = $request->notification;
                 foreach($event->booking as $book)
                 {
                     $usersids[] = ($book->user->id);
                 }

                 $users_device = DeviceInfo::whereIn('user_id',$usersids )->orderBy('id')->get();
                 foreach($users_device->chunk(500) as $key => $value) {
                     $gender = $request->ceremony_for;
                     $usersid = [];
                     $usersid[] = $value[$key]->user_id;
                     if($gender == "1" || $gender == "0" ){
                         $user_by_gender =  Customer::
                         whereIn('id',$usersid)
                             ->where('gender',"$gender")
                             ->select('id')->get();

                         $usersid = [];

                         foreach($user_by_gender as $key => $value) {

                             $usersid[] = $user_by_gender[$key]->id;

                         }
                         $users_device = DeviceInfo::whereIn('user_id',$usersid)->get();
                     }
                     ////
                     $token = [];
                     $token_data = (array) $users_device;

                     foreach($users_device as $key => $value) {
                         $token[] = $users_device[$key]->device_token;
                     }



                     $data = [
                         "to" => implode(',',$token),
                         "notification" =>
                             [
                                 "title" => "",
                                 "body" => $notification,
                                 'sound' => 'default',
                                 'badge' => '1',
                                 "icon" => url('/logo.png')
                             ],
                     ];
                     $dataString = json_encode($data);

                     $server_key = 'AAAAcboXqwo:APA91bGdDymdgWGQk07orQFVRTbzHbyZvJ4CSKXIbbpWpFphjnqYVOVJu3pmVBfalzNeXVBWrljrazmPRJe79cY1KjeAtkyg3FQrZAhIRXbe-xtOd0LN7FaxNxqdy_IylOm-sbbj0LV8';

                     $headers = [
                         'Authorization: key=' . $server_key,
                         'Content-Type: application/json',
                     ];

                     $ch = curl_init();

                     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                     curl_setopt($ch, CURLOPT_POST, true);
                     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                     $result = curl_exec($ch);

                     if ($result === FALSE) {
                         die('Oops! FCM Send Error: ' . curl_error($ch));
                     }
                     curl_close($ch);
                     //	return $result;
                     $obj 	= 	new Notification;
                     $obj->alluser 		    = 1;
                     $obj->notification 		    = $request->notification;
                     $obj->link 		= $request->link;
                     $obj->save();
                 }
             }


        }
       elseif ($for == 1){

                $body = $request->notification;

                $payment_type = $request->payment_type;

                if($payment_type == "full" || $payment_type == "down" ){

                    $user_event_details=Booking::whereIn('event_id',$request->event_id)
                        ->where("payment_type", $payment_type )
                        ->select('user_id')->get();

                }else{
                    $user_event_details=Booking::whereIn('event_id',$request->event_id)->select('user_id')->get();
                }
                $gender = $request->ceremony_for;

                $usersid = [];
                foreach($user_event_details as $key => $value) {

                    $usersid[] = $user_event_details[$key]->user_id;

                }

                if($gender == "1" || $gender == "0" ){
                    $user_by_gender =  Customer::whereIn('id',$usersid)
                        ->where('gender',"$gender")
                        ->select('id')->get();
                    $usersid = [];
                    foreach($user_by_gender as $key => $value) {
                        $usersid[] = $user_by_gender[$key]->id;
                    }
                }else{
                    $usersid = [];
                    foreach($user_event_details as $key => $value) {
                        $usersid[] = $user_event_details[$key]->user_id;
                    }
                }

                // send

                $users_device = DeviceInfo::whereIn('user_id',$usersid)->get();
                $token = [];
                $token_data = (array) $users_device;
                foreach($users_device as $key => $value) {
                    $token[] = $users_device[$key]->device_token;
                }
                $tokenString = json_encode($token);

                $notificationArray = array(
                    'title' =>"" ,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => '1');

                $arrayToSend = array(
                    'registration_ids' => $token, 'notification' => $notificationArray,'priority'=>'high');
                $data = json_encode($arrayToSend);
                $server_key = 'AAAAcboXqwo:APA91bGdDymdgWGQk07orQFVRTbzHbyZvJ4CSKXIbbpWpFphjnqYVOVJu3pmVBfalzNeXVBWrljrazmPRJe79cY1KjeAtkyg3FQrZAhIRXbe-xtOd0LN7FaxNxqdy_IylOm-sbbj0LV8';

                $headers = [
                    'Authorization: key=' . $server_key,
                    'Content-Type: application/json',
                ];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                    die('Oops! FCM Send Error: ' . curl_error($ch));
                }
                curl_close($ch);

                /// save
                foreach($request->event_id as $key => $value) {

                    $obj = new Notification;

                    $obj->alluser = 0;
                    $obj->eventid = $value;
                    $obj->notification = $request->notification;
                    $obj->ceremony_for = $request->ceremony_for;
                    $obj->link = $request->link;

                    $obj->save();
                }
        }
        else{

                $notification = $request->notification;
                $userid = $request->user_id;
                $users_device =  DeviceInfo::whereIn('user_id',$userid)->get();
                $token = $users_device[0]->device_token;
                ///
                $data = [
                    "to" => $token,
                    "notification" =>
                        [
                            "title" => "",
                            "body" => $notification,
                            'sound' => 'default',
                            'badge' => '1',
                            "icon" => url('/logo.png')
                        ],
                ];
                $dataString = json_encode($data);

                $server_key = 'AAAAcboXqwo:APA91bGdDymdgWGQk07orQFVRTbzHbyZvJ4CSKXIbbpWpFphjnqYVOVJu3pmVBfalzNeXVBWrljrazmPRJe79cY1KjeAtkyg3FQrZAhIRXbe-xtOd0LN7FaxNxqdy_IylOm-sbbj0LV8';

                $headers = [
                    'Authorization: key=' . $server_key,
                    'Content-Type: application/json',
                ];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                    die('Oops! FCM Send Error: ' . curl_error($ch));
                }
                curl_close($ch);


                ////
                $obj 	= 	new Notification;
                $obj->alluser 		    = 0;
                $obj->userid 		    =  $request->user_name;
                $obj->notification 		    = $request->notification;
                $obj->link 		= $request->link;
                $obj->save();

        }
        return $response;
    }
}
