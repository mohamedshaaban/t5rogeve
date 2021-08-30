<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

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
        App::setLocale(session('locale'));

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
//        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([  // Select2
            'label' => 'Student',
            'type' => 'relationship',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'user', // the method that defines the relationship in your Model
            'attribute' => 'all_name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'data_source' => url("/admin/fetch/bookinguser"), // url to controller search function (with /{id} should return model)
        ]);


        CRUD::addField([  // Select2
            'label' => 'Event',
            'type' => 'relationship',
            'name' => 'event_id', // the db column for the foreign key
            'entity' => 'ceremony', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to use
             'data_source' => url("/admin/fetch/ceremony"), // url to controller search function (with /{id} should return model)
        ]);
        CRUD::addField([  // Select2
            'label' => 'payment id',
             'name' => 'paymentid', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'Result',
            'type' => 'text',
            'name' => 'result', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'auth',
            'type' => 'text',
            'name' => 'auth', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'avr',
            'type' => 'text',
            'name' => 'avr', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'Refernence',
            'type' => 'text',
            'name' => 'avr', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'tran id',
            'type' => 'text',
            'name' => 'tranid', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'post date',
            'type' => 'date',
            'name' => 'postdate', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'Track id',
            'type' => 'text',
            'name' => 'trackid', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'Amount',
            'type' => 'text',
            'name' => 'amt', // the db column for the foreign key

        ]);

        CRUD::addField([  // Select2
            'label' => 'invoic id',
            'type' => 'text',
            'name' => 'invoic_id', // the db column for the foreign key

        ]);
        CRUD::addField([  // Select2
            'label' => 'phone',
            'type' => 'text',
            'name' => 'phone', // the db column for the foreign key

        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
