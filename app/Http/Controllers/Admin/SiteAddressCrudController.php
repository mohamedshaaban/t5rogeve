<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AddressRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class SiteAddressCrudController extends CrudController
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

        CRUD::setModel(\App\Models\SiteAddress::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/siteaddress');
        CRUD::setEntityNameStrings(trans('admin.siteaddress'), trans('admin.siteaddress'));
    }

    protected function setupListOperation()
    {
//        CRUD::addColumns(['content']); // add multiple columns, at the end of the stack
        $this->crud->addColumn(['name'=>'content','label'=>trans('admin.content')]);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'content',
            'label' => trans('admin.Address'),
            'type'  => 'textarea',
            'tab'   => 'Texts',

        ]);




        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
