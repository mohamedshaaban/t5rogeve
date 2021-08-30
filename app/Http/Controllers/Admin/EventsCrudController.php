<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Ceremony;
use App\Models\Customer;
use App\Models\Faculty;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
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
        CRUD::setEntityNameStrings('event', 'events');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['name', 'date','total_seats','remaining_seats','ceremony_price','number_of_students']); // add multiple columns, at the end of the stack

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => 'Event Name',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'code',
            'label' => 'Event Code',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'description',
            'label' => 'Event Description',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'address',
            'label' => 'Event address',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


         CRUD::addField([ // Text
            'name'  => 'latitude',
            'label' => 'Event latitude',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


         CRUD::addField([ // Text
            'name'  => 'longitude',
            'label' => 'Event longitude',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([ // Text
            'name'  => 'ceremony_for',
            'label' => 'Event for',
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
            'label' => 'Event date',
            'type'  => 'date',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'hideDate',
            'label' => 'hide Date',
            'type'  => 'checkbox',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                "hide" => "hide"
            ],
        ]);

        CRUD::addField([ // Text
            'name'  => 'total_seats',
            'label' => 'Event total seats',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'number_of_students',
            'label' => ' Maximum number of Students ',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'minimum_downpayment_amount',
            'label' => ' Minimum DownPayment Amount ',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'downpayment_amount2',
            'label' => ' Payment Amount 2 ',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'price',
            'label' => 'Seat Price',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'event_price',
            'label' => 'Event Price',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'free_seats',
            'label' => 'Event Price',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([ // Text
            'name'  => 'free_seats',
            'label' => 'Free Seats',
            'type'  => 'number',
            'tab'   => 'Texts',
        ]);



        CRUD::addField([ // Text
            'name'  => 'hashtag',
            'label' => 'hashtag',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([  // Select2
            'label'     => 'Faculty',
            'type'      => 'select2',
            'name'      => 'faculty', // the db column for the foreign key
            'entity'    => 'faculty', // the method that defines the relationship in your Model
            'attribute' => 'full_name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'hideSeats',
            'label' => ' Hide Seats ',
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
            'label' => ' Hide Users & Seats Number ',
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],
        ]);

        CRUD::addField([ // Text
            'name'  => 'NameExDate',
            'label' => ' Name amendment expiration date ',
            'type'  => 'date',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'RobeExDate',
            'label' => ' The expiry date of resize the robe ',
            'type'  => 'date',
            'tab'   => 'Texts',

        ]);

        $this->crud->addField([
            'label' => "Event Logo",
            'name' => "image",
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);

        $this->crud->addField([
            'label' => "Event Image",
            'name' => "imagemain",
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);

        $this->crud->addField([
            'label' => "Terms Image",
            'name' => "imageterm",
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ]);

        $this->crud->addField([
            'label' => "Description Image",
            'name' => "imagedes",
            'type' => 'image',
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
        $data = [] ;
        foreach ($events as $event)
        {
            $data[] = ['id'=>$event->id , 'name'=>$event->name];
        }

        return $data;

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

}
