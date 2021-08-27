<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SponsorPlatinumsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SponsorPlatinumsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Sponsorplatinum::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/SponsorPlatinums');
        CRUD::setEntityNameStrings('sponsorplatinum', 'sponsorplatinum');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['title']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([ // Text
            'name'  => 'image',
            'label' => 'image',
            'type'      => 'image'
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'title',
            'label' => 'Title',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'link',
            'label' => 'Link',
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
        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => 'Activated',
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "Not Active",
                1 => "Active"
            ],

        ]);
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}