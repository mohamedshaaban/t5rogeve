<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class InvoicesCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/invoices');
        CRUD::setEntityNameStrings('invoices', 'invoices');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['id']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([ // Text
            'name' => 'created_at',
            'label' => 'Date ',
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'ceremony',
            'label' => 'Event name ',
            'type' => 'relationship'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'amt',
            'label' => 'Amount',
            'type' => 'link'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'user',
            'label' => 'Student name ',
            'type' => 'relationship',
            'attribute'=>'all_name'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'phone',
'entity'=>'user',
            'label' => 'Student phone ',
            'type' => 'relationship',
            'attribute'=>'phone'
        ]);

        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
            'tab'   => 'Texts',

            // optional
            //'prefix' => '',
            //'suffix' => '',
            //'default'    => 'some value', // default value
            //'hint'       => 'Some hint text', // helpful text, show up after input
            //'attributes' => [
            //'placeholder' => 'Some text when empty',
            //'class' => 'form-control some-class'
            //], // extra HTML attributes and values your input might need
            //'wrapperAttributes' => [
            //'class' => 'form-group col-md-12'
            //], // extra HTML attributes for the field wrapper - mostly for resizing fields
            //'readonly'=>'readonly',
        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
