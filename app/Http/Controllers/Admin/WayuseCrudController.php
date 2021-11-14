<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class WayuseCrudController extends CrudController
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

        CRUD::setModel(\App\Models\WaysUse::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/wayuse');
        CRUD::setEntityNameStrings(trans('admin.wayuse'), trans('admin.wayuse'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(['name'=>'title','label'=>trans('admin.Title')]);
        $this->crud->addColumn(['name'=>'link','label'=>trans('admin.link')]);
        $this->crud->addColumn([ // Text
            'name' => 'statstitle',
            'label' => trans('admin.Active')]);

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
        CRUD::addField([ // Text
            'name'  => 'link',
            'label' => trans('admin.Link'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);

        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => trans('admin.Status'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],

        ]);

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
