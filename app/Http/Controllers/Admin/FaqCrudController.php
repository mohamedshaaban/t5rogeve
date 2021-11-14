<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaqRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class FaqCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Faq::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/faq');
        CRUD::setEntityNameStrings(trans('admin.faq'), trans('admin.faq'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(['name'=>'question','label'=>trans('admin.Question')]);
        $this->crud->addColumn(['name'=>'answer','label'=>trans('admin.Answer')]);
        $this->crud->addColumn([ // Text
            'name' => 'isactive',
            'label' => trans('admin.Active')]);

        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'question',
            'label' => trans('admin.Question'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'answer',
            'label' => trans('admin.Answer'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);

        CRUD::addField([ // Text
            'name'  => 'is_active',
            'label' => trans('admin.Active'),
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
