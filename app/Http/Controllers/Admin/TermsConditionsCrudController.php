<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContactRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TermsConditionsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\TermsCondition::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/termsconditions');
        CRUD::setEntityNameStrings('terms conditions', 'terms conditions');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['content']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([ // Text
            'type'=>'image',
            'name' => 'image',
            'label' => 'Image']);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'content',
            'label' => 'Content',
            'type'  => 'textarea',
            'tab'   => 'Texts',

        ]);

        $this->crud->addColumn([ // Text
            'type'=>'image',
            'name' => 'image',
            'label' => 'Image']);

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
