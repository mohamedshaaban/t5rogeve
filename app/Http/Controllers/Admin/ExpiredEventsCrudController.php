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

class ExpiredEventsCrudController  extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
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
        CRUD::setRoute(config('backpack.base.route_prefix').'/expiredevents');
        CRUD::setEntityNameStrings(trans('admin.event'), trans('admin.events'));
    }

    protected function setupListOperation()
    {
//        $this->crud->addClause('where', 'date',' >= ',Carbon::today()->format('Y-m-d'));
        $this->crud->addClause('whereDate', 'date',' < ',Carbon::today()->format('Y-m-d'));
//        if(backpack_user()->faculty_id!=0)
//        {
//
//            $this->crud->addClause('where', 'faculty',backpack_user()->faculty_id);
//
//        }
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


        $this->crud->addColumn(['name'=>'numstudents','label'=>trans('admin.number_of_students')]);
        $this->crud->addButtonFromModelFunction('line', 'statustext ', 'openStatus', 'beginning');
        $this->crud->enableExportButtons();
        $this->crud->disableDetailsRow();

        $this->crud->removeButton('create');

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
            'default'=>'0',
            'inline'      => true,

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
            'default'=>'0',
            'inline'      => true,
        ]);
        CRUD::addField([ // Text
            'name'  => 'hide_additional_seats',
            'label' => trans('admin.Hide Additional Seats'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],
            'default'=>'0',
            'inline'      => true,
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
            'name'  => 'Name_Ex_Date',
            'label' => trans('admin. Name amendment expiration date'),
            'type'  => 'datetime_picker',
            'format' => 'Y-m-d h:i',
            'tab'   => 'Texts'
        ]);
        CRUD::addField([ // Text
            'name'  => 'RobSize_Ex_Date',
            'label' => trans('admin. The expiry date of resize the robe'),
            'type'  => 'datetime_picker',
            'format' => 'Y-m-d h:i',
            'tab'   => 'Texts'
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
            'type' => 'ckeditor',
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

    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $response = $this->traitStore();
        \Alert::add('success', '<strong>Event Created </strong>');

        return redirect('/admin/events');
        return $response;

    }
}
