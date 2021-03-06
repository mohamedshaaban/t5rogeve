<?php

namespace App\Http\Controllers\Admin;

use App\Events\Event;
use App\Http\Requests\CustomersRequest as StoreRequest;
use App\Http\Requests\UpdateCustomersRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Booking;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class CustomersCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customers');
        CRUD::setEntityNameStrings(trans('admin.customers'), trans('admin.customers'));
    }

    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'name'        => 'id',
            'type'        => 'select2_ajax',
            'label'       => trans('admin.Student'),
            'placeholder' => 'Name , Phone Or Civil Id'
        ],
            url('admin/fetch/bookingfilteruser'), // the ajax route
            function($value) { // if the filter is active
                $this->crud->addClause('where', 'id', $value);
            });
        //  $this->crud->addColumn([ // Text
        //     'name'  => 'all_name',
        //     'label' => trans('admin.Full Name'),
        //  ]);
        $this->crud->addColumn([ // Text
            'name'  => 'phone',
            'label' => trans('admin.Phone'),
         ]);
        $this->crud->addColumn([ // Text
            'name'  => 'civil_id',
            'label' => trans('admin.civil_id'),
         ]);
        $this->crud->addColumn([ // Text
            'name' => 'active',
            'label' => trans('admin.Active'),
            'type'     => 'closure',
            'function' => function($entry) {
                if($entry->active)
                {
                    return '<button id="userActBtn'.$entry->id.'" onclick="chngStudentActive('.$entry->id.')" style="display:block;background-color: green;border-radius: 11px;" > '.trans('admin.active').' </button><button id="userNActBtn'.$entry->id.'" onclick="chngStudentActive('.$entry->id.')" style="display:none;background-color: red;border-radius: 11px;" > '.trans('admin.not_active').'</button>';
                }
                return '<button id="userActBtn'.$entry->id.'" onclick="chngStudentActive('.$entry->id.')" style="display:none;background-color: green;border-radius: 11px;" > '.trans('admin.active').' </button><button id="userNActBtn'.$entry->id.'" onclick="chngStudentActive('.$entry->id.')" style="display:block;background-color: red;border-radius: 11px;" > '.trans('admin.not_active').'</button>';

            }
        ]);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enableDetailsRow();
    }

    protected function showDetailsRow($id)
    {
        $bookings = Booking::where('user_id',$id)->orderBy('id','DESC')->get();
        $text = '<div class="row">';
        $text .= '<div class="col-12">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false">Event Name </th><th data-orderable="false">Event Date </th><th data-orderable="false">Seats</th><th data-orderable="false">Robe size </th><th data-orderable="false">Payment Type</th></tr>';
        foreach ($bookings as $booking)
        {
            $text.='<tr class="even">';
            $text.= '<td>'.@$booking->ceremony->name.'</td>';
            $text.= '<td>'.@$booking->ceremony->date.'</td>';
            $text.= '<td>'.@$booking->no_of_seats.'</td>';
            $text.= '<td>'.$booking->robe_size.'</td>';
            $text.= '<td>'.@$booking->payment_type.'</td>';
            $text.= '<td><table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
            $text .= '<tr role="row"><th data-orderable="false">paymentid</th><th data-orderable="false">Result</th><th data-orderable="false">Ref</th><th data-orderable="false">tranid</th><th data-orderable="false">amt</th></tr>';
            $payments = PaymentLog::where('user_id',$id)->where('event_id',$booking->event_id)->get();
            foreach ($payments as $payment)
            {
                $text.='<tr class="even">';
                $text.= '<td>'.@$payment->paymentid.'</td>';
                $text.= '<td>'.@$payment->result.'</td>';
                $text.= '<td>'.@$payment->ref.'</td>';
                $text.= '<td>'.@$payment->tranid.'</td>';
                 $text.= '<td>'.@$payment->amt.'</td>';
                $text.='</tr>';
            }
            $text.='</td></table>';
//            if($booking->payment_type == 'down2')
//            {
//                $text.= 'downpayment amount2 : '.@$booking->downpayment_amount2.'<br />';
//                $text.= ' Minimum DownPayment Amount  : '.@$booking->amount.'<br />';
//
//            }
//            if($booking->payment_type == 'down')
//            {
//                $text.= ' Minimum DownPayment Amount  : '.@$booking->amount.'<br />';
//            }
//            $text.= ' amount : '.@$booking->ceremony_price.'<br />';
            $text.='</tr>';
        }
        $text.='</table>';

        $text.= '</div>';
        $text .= '<div class="col-6">';
  /*      $payments = PaymentLog::where('user_id',$booking->user_id)->where('event_id',$booking->event_id)->get();
        foreach ($payments as $payment)
        {
            $text .='paymentid : ' . @$payment->paymentid.'<br />' ;
            $text .='result : '. @$payment->result.'<br />' ;
            $text .='ref : '. @$payment->ref.'<br />' ;
            $text .='tranid : '. @$payment->tranid.'<br />' ;
            $text .='trackid : '. @$payment->trackid.'<br />' ;
            $text .='amt : '. @$payment->amt.'<br />' ;

            $text.='<hr>';
        }
*/
        $text.= '</div>';
        $text.= '</div>';

        return $text;
    }
    protected function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);

    }
    protected function addUserFields()
    {
        

//        CRUD::addField([ // Text
//            'name'  => 'email',
//            'label' => trans('admin.email'),
//            'type'  => 'email',
//            'tab'   => 'Texts',
//        ]);


        CRUD::addField([ // Text
            'name'  => 'phone',
            'label' => trans('admin.phone_number'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        // $this->crud->addField([
        //     'label' => trans('admin.Image'),
        //     'name' => "image",
        //     'type' => 'image',
        //     'tab'   => 'Texts',
        //     'crop' => true, // set to true to allow cropping, false to disable
        //     'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        //     // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
        //     // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        // ]);
        $this->crud->addField([
            'label' => trans('admin.civil_id'),
            'name' => "civil_id",
            'type' => 'text',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);
        $this->crud->addField([
            'name' => "is_verified",
            'type' => 'hidden',
            'value' => 1, // omit or set to 0 to allow any aspect ratio
        ]);
        $this->crud->addField(
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
                'tab'   => 'Texts',

            ]);
        CRUD::addField([ // Text
            'name'  => 'active',
            'label' => trans('admin.active'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "not active",
                1 => "active"
            ],

        ]);
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run
        $response = $this->traitUpdate();
        return $response;
    }
    public function store()
    {

        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $response = $this->traitStore();
        return $response;
    }
        protected function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }
    public function fetchStudentDetails(Request $request)
    {
        $student = Customer::find($request->id);
        return ($student);
    }
    public function studentOptions(Request $request) {
        $term = $request->input('term');
        $options =  Customer::where('phone','like',"%".$term."%")
            ->orWhere('full_name','like',"'%".$term."%")
            ->orWhere('grandfather_name','like',"%".$term."%")
            ->orWhere('father_name','like',"%".$term."%")
            ->orWhere('family_name','like',"%".$term."%")
            ->orWhere('civil_id','like',"%".$term."%")->get();
        $data = [];
        foreach ($options as $option)
        {
            $data [$option->id] = $option->phone .' '.$option->civil_id;

        }
        return $data;
    }
    public function chngUserStatus(Request $request)
    {
        $customer = Customer::find($request->id);
        if($customer->active)
        {
            $isactive =  0;
            $customer->active = 0 ;
        }
        else
        {
            $isactive =  1;
            $customer->active = 1 ;
        }
        $customer->save();
        return $isactive;

    }
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }
}
