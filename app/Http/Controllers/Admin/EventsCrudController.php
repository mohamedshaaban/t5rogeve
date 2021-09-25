<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Ceremony;
use App\Models\Customer;
use App\Models\Faculty;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EventsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Ceremony::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/events');
        CRUD::setEntityNameStrings(trans('admin.event'), trans('admin.events'));
    }

    protected function setupListOperation()
    {
//        $this->crud->addClause('where', 'date',' >= ',Carbon::today()->format('Y-m-d'));

        if(backpack_user()->faculty_id!=0)
        {

            $this->crud->addClause('where', 'faculty',backpack_user()->faculty_id);

        }
        $dataCustomers = [];
        $customers = Faculty::all();
        foreach ($customers as $customer)
        {
            $dataCustomers[$customer->id] = $customer->full_name;
        }
        $this->crud->addFilter([
                'name'        => 'faculty',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Faculty'),
                'placeholder' => trans('admin.Faculty')

            ]
            , function()use($dataCustomers) {
                return
                    $dataCustomers
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'faculty', json_decode($value));
            });

        $this->crud->addColumn(['name'=>'name','label'=>trans('admin.Name')]);
        $this->crud->addColumn(['name'=>'date','label'=>trans('admin.date')]);
        $this->crud->addColumn(['name'=>'total_seats','label'=>trans('admin.total_seats')]);
        $this->crud->addColumn(['name'=>'ceremony_price','label'=>trans('admin.ceremony_price')]);

        $this->crud->addColumn(['name'=>'remaining_seats','label'=>trans('admin.remaining_seats')]);
        $this->crud->addColumn(['name'  => 'address', 'label' => trans('admin.Event address')]);


        $this->crud->addColumn(['name'=>'number_of_students','label'=>trans('admin.number_of_students')]);
        $this->crud->addButtonFromModelFunction('line', 'statustext ', 'openStatus', 'beginning');
        $this->crud->enableExportButtons();
        $this->crud->disableDetailsRow();

        $this->crud->disableResponsiveTable();
//        $this->crud->disablePersistentTable();

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => trans('admin.Event Name'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'code',
            'label' => trans('admin.Event Code'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

//        CRUD::addField([ // Text
//            'name'  => 'description',
//            'label' => trans('admin.Event Description'),
//            'type'  => 'text',
//            'tab'   => 'Texts',
//        ]);
        CRUD::addField([ // Text
            'name'  => 'address',
            'label' => trans('admin.Event address'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


         CRUD::addField([ // Text
            'name'  => 'latitude',
            'label' => trans('admin.Event latitude'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


         CRUD::addField([ // Text
            'name'  => 'longitude',
            'label' => trans('admin.Event longitude'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([ // Text
            'name'  => 'ceremony_for',
            'label' => trans('admin.Event for'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "Female",
                1 => "Male",
                2 => "Both"
            ],
        ]);

        CRUD::addField([ // Text
            'name'  => 'date',
            'label' => trans('admin.Event date'),
            'type'  => 'date',
            'format'   => 'Y-m-d',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'hideDate',
            'label' => trans('admin.hide Date'),
            'type'  => 'checkbox',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                "hide" => "hide"
            ],
        ]);

        CRUD::addField([ // Text
            'name'  => 'total_seats',
            'label' => trans('admin.Event total seats'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'number_of_students',
            'label' => trans('admin. Maximum number of Students'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'minimum_downpayment_amount',
            'label' => trans('admin. Minimum DownPayment Amount'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'downpayment_amount2',
            'label' => trans('admin. Payment Amount 2'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'price',
            'label' => trans('admin.Seat Price'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'event_price',
            'label' => trans('admin.Event Price'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);



        CRUD::addField([ // Text
            'name'  => 'free_seats',
            'label' => trans('admin.Free Seats'),
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);



        CRUD::addField([ // Text
            'name'  => 'hashtag',
            'label' => trans('admin.hashtag'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([  // Select2
            'label'     => trans('admin.Faculty'),
            'type'      => 'select2',
            'name'      => 'faculty', // the db column for the foreign key
            'entity'    => 'faculty', // the method that defines the relationship in your Model
            'attribute' => 'full_name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'default'=>backpack_user()->faculty_id
        ]);

        CRUD::addField([ // Text
            'name'  => 'hideSeats',
            'label' => trans('admin. Hide Seats'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],
        ]);

        CRUD::addField([ // Text
            'name'  => 'hide_UsersSeatsN',
            'label' => trans('admin. Hide Users & Seats Number'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],
        ]);
        $this->crud->addField([
            'label' => trans("admin.link_store"),
            'name' => "link_store",
            'type' => 'text',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);

        $this->crud->addField([
            'label' => trans("admin.Link Store Image"),
            'name' => "link_store_image",
            'hint'=>'1078 ْX 275',
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio

        ]);


        CRUD::addField([ // Text
            'name'  => 'NameExDate',
            'label' => trans('admin. Name amendment expiration date'),
            'type'  => 'date',
            'format'   => 'Y-m-d',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'RobeExDate',
            'label' => trans('admin. The expiry date of resize the robe'),
            'type'  => 'date',
            'format'   => 'Y-m-d',
            'tab'   => 'Texts',

        ]);

        $this->crud->addField([
            'label' => trans("admin.Event Logo"),
            'name' => "image",
            'type' => 'image',
            'hint'=>'900 X 900',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);

        $this->crud->addField([
            'label' => trans("admin.Event Image"),
            'name' => "imagemain",
            'type' => 'image',
            'hint'=>'900 ْX 650',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);


        $this->crud->addField([
            'label' => trans("admin.Terms"),
            'name' => "imageterm",
            'type' => 'hidden',
            'hint'=>'',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);
/*
        $this->crud->addField([
            'label' => trans("admin.Description"),
            'name' => "amenities",
            'type' => 'relationship',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio

            // optional
            'entity'    => 'amenities', // the method that defines the relationship in your Model
            'model'     => "App\Models\amenities", // foreign key model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?

            // also optional
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);
*/

        $this->crud->addField([
            'label' => trans("admin.Event Term Image"),
            'name' => "imageterm2",
            'type' => 'image',
            'hint'=>'734 X 44000',

            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio

        ]);
        $this->crud->addField([
            'label' => trans("admin.Event details"),
            'name' => "imagedes",
            'type' => 'image',
            'hint'=>'660 X 660',

            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio

        ]);
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $events = Ceremony::where('name','like','%'.$request->q.'%')->get(['id','name']);

        if(backpack_user()->faculty_id!=0)
        {
            $events = Ceremony::where('name','like','%'.$request->q.'%')->where('faculty',backpack_user()->faculty_id)->get(['id','name']);

        }
        $data = [] ;
        foreach ($events as $event)
        {
            $data[] = ['id'=>$event->id , 'name'=>$event->name];
        }

        return $data;

    }
    public static function fetchEventDetails(\Illuminate\Http\Request  $request)
    {
        $event = Ceremony::find($request->id);
        return ($event);
    }
    public static function fetchDashEventDetails(\Illuminate\Http\Request  $request)
    {
        $event = Ceremony::find($request->id);
        return ([
             'eveDetBookSeats'=>$event->total_seats.'/'.$event->remaining_seats,
            'eveDetAmtFull'=>$event->booking()->where('payment_type','full')->sum('amount'),
            'eveDetAmtDwnPay'=>$event->booking()->where('payment_type','down')->sum('amount'),
            'eveDetAmtRem'=>$event->booking()->where('payment_type','down')->sum('remaining_amount'),
            'eveDetRegUser'=>$event->booking()->count('id')
        ]);
    }

    public static function fetchuser(\Illuminate\Http\Request  $request)
    {
        $users = Customer::where('phone','like','%'.$request->q.'%')
            ->orWhere('full_name','like','%'.$request->q.'%')
            ->orWhere('grandfather_name','like','%'.$request->q.'%')
            ->orWhere('father_name','like','%'.$request->q.'%')
            ->orWhere('family_name','like','%'.$request->q.'%')
            ->get();
        $data = [] ;
        foreach ($users as $user)
        {
            $data[] = ['id'=>$user->id , 'all_name'=>$user->full_name.' '.$user->grandfather_name.' '.$user->father_name.' - '.$user->phone];
        }

        return $data;

    }
    public function eventOptions(Request $request) {
        $term = $request->input('term');
        $options =  Ceremony::where('name','like','%'.$term.'%')
            ->get();
        $data = [];
        foreach ($options as $option)
        {
            $data [$option->id] = $option->name;

        }
        return $data;
    }
}
