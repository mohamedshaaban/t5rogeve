<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Cars;
use App\Models\Faculty;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class FacultyCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Faculty::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/faculty');
        CRUD::setEntityNameStrings(trans('admin.Faculty'), trans('admin.Faculty'));
    }

    protected function setupListOperation()
    {
//        CRUD::addColumns(['full_name']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([
            'name'  => 'full_name',
            'label' => trans('admin.Faculty name')
        ]);
        $this->crud->addButtonFromModelFunction('line', 'statustext ', 'openStatus', 'beginning');


    }

    protected function setupCreateOperation()
    {
//        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'full_name',
            'label' => trans('admin.Faculty name'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => trans('admin.Activated'),
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
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $areas = Faculty::where('full_name','like','%'.$request->q.'%')->get(['id','full_name']);
        $data = [] ;
        foreach ($areas as $area)
        {
            $data[] = ['id'=>$area->id , 'full_name'=>$area->full_name];
        }

        return $data;

    }
}
