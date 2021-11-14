<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class WhoweareCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Whoweare::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/whoweare');
        CRUD::setEntityNameStrings(trans('admin.whoweare'), trans('admin.whoweare'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(['name'=>'title','label'=>trans('admin.Title')]);

        $this->crud->addColumn([ // Text
            'type'=>'image',
            'name' => 'image',
            'label' => trans('admin.Image')]);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
     }

    protected function setupCreateOperation()
    {
//        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'title',
            'label' => trans('admin.Title'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
//
        $this->crud->addField([
            'label' => trans('admin.Image'),
            'name' => "image",
            'type' => 'image',
            'tab'   => 'Texts',
            'crop' => false, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
