<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContactRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class ContactCrudController extends CrudController
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

        CRUD::setModel(\App\Models\ContactU::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/contact');
        CRUD::setEntityNameStrings(trans('admin.contact'), trans('admin.contact'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name'=>'name',
            'label'=>trans('admin.Name')
        ]);
        $this->crud->addColumn([
            'name'=>'email',
            'label'=>trans('admin.email')
        ]);
        $this->crud->addColumn([
            'name'=>'mobile',
            'label'=>trans('admin.phone')
        ]);
        $this->crud->addColumn([
            'name'=>'subject',
            'label'=>trans('admin.Subject')
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'reply',
            'label' => trans('admin.Is Replied'),
            'type'     => 'closure',
            'function' => function($entry) {
                if($entry->isreply)
                {
                    return '<span style="background-color: green;border-radius: 11px;" > '.trans('admin.Replied').' </span>';
                }
                return '<span style="background-color: red;border-radius: 11px;" > '.trans('admin.Not Replied').'</span>';

            }
        ]);
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
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
        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => trans('admin.Name'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'email',
            'label' => trans('admin.Email'),
            'type'  => 'email',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'mobile',
            'label' => trans('admin.Mobile'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'subject',
            'label' => trans('admin.Subject'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'reply',
            'label' => trans('admin.Reply'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
