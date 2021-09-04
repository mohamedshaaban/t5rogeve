<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PollRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\PollAnswered;
use App\Models\PollOption;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class PollsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Poll::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/polls');
        CRUD::setEntityNameStrings(trans('admin.polls'), trans('admin.polls'));
    }

    protected function setupListOperation()
    {$this->crud->addColumn(['name'=>'question','label'=>trans('admin.question')]);
        CRUD::addColumns(['question']); // add multiple columns, at the end of the stack
        $this->crud->addColumn([ // Text
            'name' => 'ceremony',
            'label' => trans('admin.Event name'),
            'type' => 'relationship'
        ]);

        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
        $this->crud->enableDetailsRow();
    }

    protected function showDetailsRow($id)
    {
        $polloptions = PollOption::with('pollanswered')->where('poll_id',$id)->get();
        $pollanswered = PollAnswered::where('poll_id',$id)->get();
         $text = '<div class="row">';
        $text .= '<div class="col-md-6 col-sm-12">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false" style="width:30%">Option</th><th width="30%" data-orderable="false">Count </th></tr>';
        foreach($polloptions as $polloption)
        {
            $text.='<tr class="even">';
            $text.= '<td  style="width:30%">'.$polloption->answer.'</td>';
            $text.= '<td>'.@$polloption->pollanswered->count().'</td>';
            $text.='</tr>';
        }
        $text.='</table></div>';
        $text .= '<div class="col-md-6 col-sm-12">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false" style="width:30%">Student</th><th width="30%" data-orderable="false">Answer </th></tr>';
        foreach($pollanswered as $pollanswer)
        {
            $text.='<tr class="even">';
            $text.= '<td  style="width:30%">'.$pollanswer->user->all_name.'</td>';
            $text.= '<td>'.@$pollanswer->polloption->answer.'</td>';
            $text.='</tr>';
        }
        $text.='</table></div>';
        $text .= '<div class="col-sm-4">';
        $text.='</div>';
        $text.='</div>';
        return $text;
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
        CRUD::addField([   // repeatable
            'name'  => 'polloption',
            'label' => trans('admin.Poll Options'),
            'type'  => 'repeatable',
            'tab'   => 'Texts',

            'fields' => [
                [
                    'name'    => 'answer',
                    'type'    => 'text',
                    'label'   => 'Answer',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],

            ],

            // optional
            'new_item_label'  => 'Add Answer', // customize the text of the button

        ],);


        CRUD::addField([ // Text
            'name'  => 'startDate',
            'label' => trans('admin.start Date'),
            'type'  => 'date',
            'tab'   => 'Texts',

        ]);

        CRUD::addField([ // Text
            'name'  => 'endDate',
            'label' => trans('admin.end Date'),
            'type'  => 'date',
            'tab'   => 'Texts',

        ]);



        CRUD::addField([  // Select2
            'label' => trans('admin.Event'),
            'type' => 'select2',
            'name' => 'eventid', // the db column for the foreign key
            'entity' => 'ceremony', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'attributes' => [
                'class'       => 'form-control event-class'],
            'data_source' => url("/admin/fetch/ceremony"), // url to controller search function (with /{id} should return model)
        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function store(\Illuminate\Http\Request  $request)
    {
        $this->crud->unsetValidation(); // validation has already been run
        $form = backpack_form_input();
        $response = $this->traitStore();
        foreach (json_decode($request->polloption) as $polloption)
        {
            PollOption::create(['poll_id'=>$this->crud->entry->id,
                'answer'=>$polloption->answer]);
        }
        return $response;
    }
    public function update(\Illuminate\Http\Request  $request)
    {
        $this->crud->unsetValidation(); // validation has already been run
        $form = backpack_form_input();
        $response = $this->traitUpdate();
        foreach (json_decode($request->polloption) as $polloption)
        {
            PollOption::updateOrCreate(['poll_id'=>$this->crud->entry->id,
                'answer'=>$polloption->answer],['poll_id'=>$this->crud->entry->id,
                'answer'=>$polloption->answer]);
        }
        return $response;
    }
}
