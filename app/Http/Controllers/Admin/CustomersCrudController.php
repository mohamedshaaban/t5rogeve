<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CustomersCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customers');
        CRUD::setEntityNameStrings('customers', 'customers');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['title']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([ // Text
            'name'  => 'first_name',
            'label' => 'first name',
         ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'first_name',
            'label' => 'first name',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'father_name',
            'label' => 'father name',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'grandfather_name',
            'label' => 'grandfather name',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'family_name',
            'label' => 'family name',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'email',
            'label' => 'email',
            'type'  => 'email',
            'tab'   => 'Texts',
        ]);


        CRUD::addField([ // Text
            'name'  => 'phone_number',
            'label' => 'phone_number',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);


        $this->crud->addField([
            'label' => "Image",
            'name' => "image",
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);
        $this->crud->addField([
            'label' => "civil_id",
            'name' => "civil_id",
            'type' => 'text',
            'tab'   => 'Texts',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);
        CRUD::addField([ // Text
            'name'  => 'faulty',
            'label' => 'faulty',
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

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
