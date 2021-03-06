<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CanceledeventsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class CancelEventSubCrudController extends CrudController
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

        CRUD::setModel(\App\Models\CancelEventSub::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/canceleventsub');
        CRUD::setEntityNameStrings(trans('admin.canceleventsub'), trans('admin.canceleventsub'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([ // Text
            'name' => 'ceremony',
            'label' => trans('admin.Event name'),
            'type' => 'relationship'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'user',
            'label' => trans('admin.Student name'),
            'type' => 'relationship',
            'attribute'=>'all_name'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'statstitle',
            'label' => trans('admin.Canceled')]);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addField([  // Select2
            'label' => trans('admin.Student'),
            'type' => 'relationship',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'user', // the method that defines the relationship in your Model
            'attribute' => 'all_name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'data_source' => url("/admin/fetch/bookinguser"), // url to controller search function (with /{id} should return model)
        ]);

        CRUD::addField([  // Select2
            'label' => trans('admin.ceremony'),
            'type' => 'relationship',
            'name' => 'event_id', // the db column for the foreign key
            'entity' => 'ceremony', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'data_source' => url("/admin/fetch/ceremony"), // url to controller search function (with /{id} should return model)
        ]);
        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => trans('admin.Canceled'),
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
