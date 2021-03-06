<?php
namespace App\Http\Controllers\Admin;
use App\Http\Requests\BookingRequest as StoreRequest;

// VALIDATION: change the requests to match your own file names if you need form validation

use App\Models\Booking;
use App\Models\Cars;
use App\Models\Ceremony;
use App\Models\Faculty;
use App\Models\Payment;
use App\Models\PaymentLog;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class BookingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    public function setup()
    {
        App::setLocale(session('locale'));
        CRUD::setModel(\App\Models\Booking::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/booking');
        CRUD::setEntityNameStrings(trans('admin.booking'), trans('admin.booking'));
    }

    protected function setupListOperation()
    {

         if (backpack_user()->faculty_id != 0) {
            $eventIds = Ceremony::where('faculty', backpack_user()->faculty_id)->pluck('id')->toArray();
           $this->crud->addClause('whereIn', 'event_id', $eventIds);
        }
        $this->crud->addClause('orderBy', 'id','DESC');
        $this->crud->addClause('whereHas', 'ceremony');
        $this->crud->addFilter([
            'name' => 'user_id',
            'type' => 'select2_ajax',
            'label' => trans('admin.Student'),
            'placeholder' => 'Name , Phone Or Civil ID '
        ],
            url('admin/fetch/bookingfilteruser'), // the ajax route
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'user_id', $value);
            });
        $this->crud->addFilter([
            'name' => 'event_id',
            'type' => 'select2_ajax',
            'label' => trans('admin.Event'),
            'placeholder' => 'Pick a event'
        ],
            url('admin/fetch/eventfilteruser'), // the ajax route
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'event_id', $value);
            });
            $this->crud->addColumn([ // Text
            'name' => 'id',
            'orderable' => false,
            'label' => trans('admin.Id'),
        ]);
       $this->crud->addColumn([ // Text
            'name' => 'created_at',
            'orderable' => false,
            'label' => trans('admin.Date'),
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'ceremony',
            'orderable' => false,
            'label' => trans('admin.Event name'),
            'type' => 'relationship'
        ]);
      $this->crud->addColumn([ // Text
            'name' => 'all_name',
             'label' => trans('admin.Student name'),
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('full_name', $columnDirection);
            }
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'no_of_seats',
            'label' => trans('admin.Seats Book'),
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('no_of_seats', $columnDirection);
            }
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'payment_type',
             'label' => trans('admin.Payment Type'),
            'orderable' => true,
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'ceremony_price',
            'label' => trans('admin.Event Price'),
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('booking.ceremony_price', $columnDirection);
            }
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'amount',
            'label' => trans('admin.Paid Amount'),
            'orderable' => false,
            
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'remaining_amount',
            'label' => trans('admin.Remaining Amount'),
            'orderable' => false,
            
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'robe_size',
            'label' => trans('admin.Robe Size'),
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'phone',
            'entity' => 'user',
            'label' => trans('admin.Student phone'),
            'type' => 'relationship',
            'attribute' => 'phone'
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'civil_id',
            'entity' => 'user',
            'label' => trans('admin.civil_id'),
            'type' => 'relationship',
            'attribute' => 'civil_id'
        ]);
        // $this->crud->addButtonFromModelFunction('line', 'share ', 'openGoogle', 'beginning');
        
         if(\Request::input('event_id'))
        {
            $this->crud->setDefaultPageLength(400);
        }
        else
        {
        $this->crud->setDefaultPageLength(25);    
        }
        
        $this->crud->enableExportButtons();
        $this->crud->enableDetailsRow();
        $this->crud->disableBulkActions();
        $this->crud->enableResponsiveTable();
     }

    protected function showDetailsRow($id)
    {
        $booking = Booking::find($id);
        $text = '<div class="row">';
        $text .= '<div class="col-4">';
        $text .= 'Student Phone: ' . @$booking->user->phone . '<br />';
        $text .= 'Seats: ' . @$booking->no_of_seats . '<br />';
        $text .= 'Robe size : ' . @$booking->robe_size . '<br />';
        $text .= 'Payment Type : ' . @$booking->payment_type . '<br />';
        if ($booking->payment_type == 'down2') {
            $text .= 'downpayment amount2 : ' . @$booking->downpayment_amount2 . '<br />';
            $text .= ' Minimum DownPayment Amount  : ' . @$booking->amount . '<br />';
        }
       if ($booking->payment_type == 'down') {
            $text .= ' Minimum DownPayment Amount  : ' . @$booking->amount . '<br />';
        }
        $text .= ' amount : ' . @$booking->ceremony_price . '<br />';
        $text .= '</div>';
        $text .= '<div class="col-4">';
        $payments = PaymentLog::where('user_id', $booking->user_id)->where('event_id', $booking->event_id)->get();
        foreach ($payments as $payment) {
            $text .= 'paymentid : ' . @$payment->paymentid . '<br />';
            $text .= 'result : ' . @$payment->result . '<br />';
            $text .= 'ref : ' . @$payment->ref . '<br />';
            $text .= 'tranid : ' . @$payment->tranid . '<br />';
            $text .= 'trackid : ' . @$payment->trackid . '<br />';
            $text .= 'amt : ' . @$payment->amt . '<br />';
            $text .= '<hr>';
        }
        $text .= '</div>';
        $text .= '</div>';
        return $text;
    }
    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addField([  // Select2
            'label' => trans('admin.Mobile'),
            'type' => 'relationship',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'user', // the method that defines the relationship in your Model
            'attribute' => 'phone', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control student-class'],
            'data_source' => url("/admin/fetch/bookingphoneuser"), // url to controller search function (with /{id} should return model)
        ]);
        $event = null;
        $booking = null;
        $total_amount = 0;
        $freeseats = 0;
        $seats = 0;
        $eventPrice = 0;

        if ((request()->route('id'))) {

            $booking = Booking::find(request()->route('id'));
            $seats = $booking->no_of_seats;
            $freeseats = $booking->freeseats;
            // dd($freeseats);
            $eventPrice = $booking->ceremony_price;

            $event = Ceremony::where('id', $booking->event_id)->first();
            
            (session(['evtId'=>$event->id]));
            if ($event) {
                // if ($freeseats == 0) {
                    $freeseats = $event->free_seats;
                    
                    $seats = $booking->no_of_seats - $freeseats;
                // }


                $total_amount = $event->ceremony_price + ($booking->no_of_seats - $freeseats) * $event->price;
                if ($eventPrice != $event->ceremony_price) {
                    $eventPrice = $event->ceremony_price;
                }
            }
        }
//        CRUD::addField([ // Text
//            'name' => 'mobile',
//            'label' => trans('admin.Mobile'),
//            'type' => 'text',
//            'tab' => 'Texts',
//            'attributes' => [
//                'class'       => 'form-control booking-mobile'],
//        ]);
        CRUD::addField([ // Text
            'name' => 'full_name',
            'label' => trans('admin.First name'),
            'type' => 'text',
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control booking-full_name'],
        ]);
        CRUD::addField([ // Text
            'name' => 'father_name',
            'label' => trans('admin.Second name'),
            'type' => 'text',
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control booking-father_name'],
        ]);
        CRUD::addField([ // Text
            'name' => 'grandfather_name',
            'label' => trans('admin.Third name'),
            'type' => 'text',
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control booking-grandfather_name'],
        ]);
        CRUD::addField([ // Text
            'name' => 'family_name',
            'label' => trans('admin.Forth name'),
            'type' => 'text',
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control booking-family_name'],
        ]);
        CRUD::addField([  // Select2
            'label' => trans('admin.Event'),
            'type' => 'relationship',
            'name' => 'event_id', // the db column for the foreign key
            'entity' => 'ceremony', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'allows_null' => true,
            'attributes' => [
                'class' => 'form-control event-class'],
            'data_source' => url("/admin/fetch/ceremony"), // url to controller search function (with /{id} should return model)
        ]);
        $this->crud->addField([  // Select2
                'label' => trans('admin. Payment Type'),
                'type' => 'select_from_array',
                'name' => 'payment_type', // the db column for the foreign key
                'default' => 'down',
                'tab' => 'Texts',
                'options' => [
                    'full' => 'full',
                    'down2' => 'Down2',
                    'down' => 'Down',
                    'down3' => 'Down3'
                ],
                'attributes' => [
                    'class' => 'form-control payment-class']
            ]
        );
        CRUD::addField([ // Text
            'name' => 'freeseats',
            'label' => trans('admin.Free Seats'),
            'type' => 'number',
            'tab' => 'Texts',
            'value' => $freeseats,
            'attributes' => [
                'class' => 'form-control freeseats-class'],
        ]);

        CRUD::addField([ // Text
            'name' => 'ceremony_price',
            'label' => trans('admin.Event Price'),
            'type' => 'number',
            'tab' => 'Texts',
           'value' => $eventPrice,
            // 'default'=>isset($event->ceremony_price)?$event->ceremony_price:0,
            'attributes' => [
                'class' => 'form-control eventprice-class'],
        ]);

        CRUD::addField([ // Text
            'name' => 'no_of_seats',
            'label' => trans('admin.Seats Book To Buy'),
            'type' => 'number',
            'tab' => 'Texts',
            'value' => $seats,
            'attributes' => [
                'class' => 'form-control noseats-class'],
        ]);

        CRUD::addField([   // CustomHTML
            'name' => 'separator',
            'type' => 'custom_html',
            'tab' => 'Texts',
            'value' => '<input type="text" class="hideevent-class" style="display:none" id="myInput"/>']);
        CRUD::addField([  // Select2
                'label' => trans('admin.Robe Size'),
                'type' => 'select2_from_array',
                'name' => 'robe_size', // the db column for the foreign key
                'allows_null' => true,
                'tab' => 'Texts',
                'options' => [
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    '3XL' => '3XL',
                    '4XL' => '4XL',
                    '5XL' => '5XL',
                ]]
        );
        $this->crud->addField([ // Text
            'name' => 'downpayment_amount1',
            'label' => trans('admin. Minimum DownPayment Amount'),
            'type' => 'number',
            'tab' => 'Texts',
            'value' => isset($booking) ? $booking->downpayment_amount1 : 0,
            'attributes' => [
                'class' => 'form-control downpayment-class'],
            "visibility" => [
                'field_name' => 'payment_type',
                'value' => 'Down',
                'add_disabled' => true, // if you need to disable this field value to not be send through create/update request set it to true, otherwise set it to true
            ],
        ]);

        CRUD::addField([ // Text
            'name' => 'downpayment_amount2',
            'label' => trans('admin.Payment Amount 2'),
            'type' => 'number',
            'tab' => 'Texts',
            'value' => isset($booking) ? $booking->downpayment_amount2 : 0,
            //  'default'=>isset($event->downpayment_amount2)?$event->downpayment_amount2:0,
            'attributes' => [
                'class' => 'form-control payment-class2']
        ]);

        CRUD::addField([ // Text
            'name' => 'downpayment_amount3',
            'label' => trans('admin.Payment Amount 3'),
            'type' => 'number',
            'value' => isset($booking) ? $booking->downpayment_amount3 : 0,
            'tab' => 'Texts',
            'attributes' => [
                'class' => 'form-control payment-class3']
        ]);

        CRUD::addField([ // Text
            'name' => 'total_amount',
            'label' => trans('admin.total_amount'),
            'type' => 'number',
           'tab' => 'Texts',
            'default' => $total_amount,
            'attributes' => [
                'class' => 'form-control amt3']
        ]);
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    protected function setupUpdateOperation()
    {
       $this->setupCreateOperation();
    }
    public static function fetch(\Illuminate\Http\Request $request)
    {
        $areas = Faculty::where('full_name', 'like', '%' . $request->q . '%')->get(['id', 'full_name']);
        $data = [];
        foreach ($areas as $area) {
            $data[] = ['id' => $area->id, 'full_name' => $area->full_name];
        }
       return $data;
    }
    public function store()
    {
        // $this->crud->unsetValidation(); // validation has already been run
        $form = backpack_form_input();
        $event_id = $this->crud->getRequest()->event_id;
        $user_id = $this->crud->getRequest()->user_id;
          $booking = Booking::where('event_id',$event_id)->where('user_id',$user_id)->delete();
         PaymentLog::where(
            'user_id',$user_id)->where(
            'event_id',$event_id)->delete();
      
        $response = $this->traitStore();
       $invoice = Booking::find($this->crud->entry->id);
        $total = 0;
        $event = Ceremony::find($invoice->event_id);
        $invoice->no_of_seats= $invoice->no_of_seats + $event->free_seats;
        $extra_seats = $invoice->no_of_seats - $event->free_seats;
        $extra_seat_total = $extra_seats *$event->price;
        $invoice->freeseats=0;
        $invoice->save();
        //|||||||||| payment type == full ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        $freeseats = $invoice->freeseats;
        $event_price = $invoice->event_price;
        $amount = $invoice->amount;
        $input_seats = $invoice->seats;
        $payment_type = $invoice->payment_type;
        $seatPrice = $invoice->amountper;
        $seatsPrice = $input_seats * $seatPrice;
        $TotalPrice = $invoice->total_amount;
        if ($payment_type == "down" || $payment_type == "down2") {
            if ($freeseats > 0) {
               $price = $amount;
                $totalseat = $input_seats + $freeseats;
            } else {
                $price = $amount;
                $totalseat = $input_seats;
            }
        } else {
            if ($freeseats > 0) {
                $price = $TotalPrice;
                $totalseat = $input_seats + $freeseats;
            } else {
                $price = $TotalPrice;
                $totalseat = $input_seats;
            }
        }
        $remaining_amount = 0;
        if ($invoice->payment_type == 'down') {
            $remaining_amount = $event_price - $invoice->amount;
        } else if ($invoice->payment_type == 'down2') {
            $remaining_amount = $event_price - $invoice->amount - $invoice->downpayment_amount2;
        }
        $payValue = 0;
        if ($payment_type == "down") {
            $payValue = $amount;
        } else if ($payment_type == "down2") {
            $payValue = $amount + $invoice->downpayment_amount2;
        } else {
            $payValue = $price;
        }
        $userid = $invoice->user_id;
        $eventid = $invoice->event_id;
       $payment_arr = [
            'user_id' => $invoice->user_id,
            'ceremony_id' => $invoice->event_id,
            'booking_id' => $this->crud->entry->id,
            'status' => 1,
            'price' => $payValue,
            'payment_method' => $invoice->payment_type,
        ];
        $pay_obj = new Payment();
       foreach ($payment_arr as $key => $value) {
            $pay_obj->$key = $value;
        }
        $payment_dtl = $pay_obj->save();
        //update booking field
        $val =
            Ceremony::where('id', $invoice->event_id)
               ->first();
        $totalseat_hwe = $val->remaining_seats;
        $remainseat = $totalseat_hwe - $totalseat;
        Ceremony::where('id', $invoice->event_id)->update(['remaining_seats' => $remainseat]);
        $invoice = Booking::find($this->crud->entry->id);
        $BookingId = $invoice->id;
         $payment_type = $invoice->payment_type;
                //|||||||||| payment type == full ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'full') // && $no_of_seats<$input_seats
        {
            // $remaining_seat = $input_seats - $no_of_seats;
            $diff_seats = 0;
            $new_total_seats = 0;
            // check if there is avalibale seats
            // if ($input_seats > $no_of_seats) {
            //     if (!$this->checkSeatAvailabilityForEvent($eventid, $input_seats)) {
            //         return $response;
            //     }
            // }
            $price = $invoice->total_amount;
            // Booking::where('id', $invoice->id)
            //     ->update(
            //         array(
            //             // 'user_id'		=> $request->user_name'),
            //             // 'event_id' => $invoice->event_name,
            //             'no_of_seats' => $totalseat,
            //             'amount' => $full_amount,
            //             'robe_size' => $robesize,
            //             'remaining_amount' => 0,
            //             'payment_type' => 'full'
            //         ));
            //update ceremony field
            $val = Ceremony::where('id', $eventid)
                ->first();
            $totalremainseat = $val->remaining_seats;
            // $remainseat = $totalremainseat - $remaining_seat;
            Ceremony::where('id', $eventid)->update(['remaining_seats' => $remainseat]);
            (PaymentLog::create(['amt' => $invoice->total_amount,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'event_id' => $eventid]));


            
        }
        //|||||||||| payment type == down ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'down') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount1;
            // Booking::where('id', $BookingId)
            //     ->update(
            //         array(
            //             // 'event_id' 		=> $request->event_name'),
            //             // 'user_id'		=> $request->user_name'),
            //             // 'event_id' => $invoice->event_name,
            //             'no_of_seats' => $freeSeats,
            //             'amount' => $price,
            //             'robe_size' => $robesize,
            //             'remaining_amount' => $remaining_amount,
            //             'payment_type' => 'down'
            //         ));
                                (PaymentLog::create(['amt' => $extra_seat_total +$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down1']));
            Payment::where('booking_id', $BookingId)->update(['price' => $price, 'payment_method' => 'down']);
            
        }
         //|||||||||| payment type == down2 ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'down2') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount2;
            // Booking::where('id', $BookingId)
            //     ->update(
            //         array(
            //             // 'event_id' => $invoice->event_id,
            //             'no_of_seats' => $freeSeats,
            //             'amount' => $invoice->amount + $invoice->downpayment_amount2,
            //             'robe_size' => $robesize,
            //             'remaining_amount' => $ceremony_price - $invoice->amount - $invoice->downpayment_amount2,
            //             'payment_type' => 'down'
            //         ));
                     ($extra_seat_total +$invoice->downpayment_amount1);
                     (PaymentLog::create(['amt' => $extra_seat_total +$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down1']));
                    
                    
                                     
                     (PaymentLog::create(['amt' => $invoice->downpayment_amount2,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down2']));
                    
            Payment::where('booking_id', $BookingId)->update(['price' => $invoice->amount + $invoice->downpayment_amount2, 'payment_method' => 'down2']);
        }
         if ($payment_type == 'down3') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount3;
            Booking::where('id', $BookingId)
                ->update(
                    array(
                        'payment_type' => 'full'
                    ));
                    (PaymentLog::create(['amt' => $extra_seat_total+$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down1']));
                    
                    
                                     
                    (PaymentLog::create(['amt' => $invoice->downpayment_amount2,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down2']));
        
                    
                    
                                     
                    (PaymentLog::create(['amt' => $invoice->downpayment_amount3,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down3']));
                    
            Payment::where('booking_id', $BookingId)->update(['price' => $invoice->amount + $invoice->downpayment_amount2, 'payment_method' => 'down3']);
        }
        return $response;
    }
    public function update()
    {
        $id = (request()->route('id'));
         $oldBook = Booking::find($id);
        //  dd(($this->crud->getRequest()->event_id));
         if(!$this->crud->getRequest()->event_id)
         {
             $this->crud->getRequest()->event_id = $oldBook->event_id;
         }
        
        $this->crud->setRequest($this->crud->validateRequest());
        // $this->crud->unsetValidation(); // validation has already been run
        $booking = Booking::where('id',$id)->first();
         PaymentLog::where(
            'user_id',$booking->user_id)->where(
            'event_id',$booking->event_id)->delete();
            $booking->delete();
       $response = $this->traitStore();

    //   dd($response);
        $invoice = Booking::find($this->crud->entry->id);

        $event = Ceremony::find($invoice->event_id);
        $BookingId = $this->crud->entry->id;
        $input_seats = $invoice->seats;
        $extra_seats = $invoice->no_of_seats - $event->free_seats;
        
        $extra_seat_total = $extra_seats *$event->price;
        $invoice->no_of_seats= $invoice->no_of_seats + $event->free_seats;
        
        $invoice->freeseats= $event->free_seats;
        $invoice->save();
        $price = $invoice->amount;
        $booking = Booking::with('ceremony')->find($BookingId);
        $eventid = $booking->event_id;
        $userid = $booking->user_id;
        $ceremony_price = $invoice->event_price;
        $freeSeats = $booking->ceremony->free_seats;
        $prev_amount = $booking->amount;
        $no_of_seats = $booking->no_of_seats;
        $seatPrice = $invoice->amountper;
        $robesize = $invoice->robe_size;
        $payment_type = $invoice->payment_type;
        $remaining_amount = $ceremony_price - $price;
        $totalseat = $freeSeats + $input_seats;
        // dd($freeSeats,$no_of_seats,$totalseat);
        $seatsPrice = $input_seats * $seatPrice;
        $full_amount = $invoice->total_amount;
        //|||||||||| payment type == full ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'full') // && $no_of_seats<$input_seats
        {
            $remaining_seat = $input_seats - $no_of_seats;
            $diff_seats = 0;
            $new_total_seats = 0;
            // check if there is avalibale seats

            $price = $invoice->total_amount;
            Booking::where('id', $invoice->id)
                ->update(
                    array(
                        // 'user_id'		=> $request->user_name'),
                        // 'event_id' => $invoice->event_name,
                      
                        'remaining_amount' => 0,
                        'payment_type' => 'full'
                    ));
            //update ceremony field
            $val = Ceremony::where('id', $eventid)
                ->first();
            $totalremainseat = $val->remaining_seats;
            $remainseat = $totalremainseat - $remaining_seat;
            Ceremony::where('id', $eventid)->update(['remaining_seats' => $remainseat]);
            (PaymentLog::create(['amt' => $invoice->total_amount,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'event_id' => $eventid]));

            
        }
        //|||||||||| payment type == down ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'down') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount1;
            Booking::where('id', $BookingId)
                ->update(
                    array(
                     
                        'remaining_amount' => $remaining_amount,
                        'payment_type' => 'down'
                    ));
            (PaymentLog::create(['amt' => $extra_seat_total+$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'event_id' => $eventid]));
            Payment::where('booking_id', $BookingId)->update(['price' => $price, 'payment_method' => 'down']);
            
        }
        //|||||||||| payment type == down2 ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        if ($payment_type == 'down2') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount2;
            Booking::where('id', $BookingId)
                ->update(
                    array(
                        // 'event_id' => $invoice->event_id,
                        'no_of_seats' => $freeSeats,
                        'amount' => $invoice->amount + $invoice->downpayment_amount2,
                        'robe_size' => $robesize,
                        'remaining_amount' => $ceremony_price - $invoice->amount - $invoice->downpayment_amount2,
                        'payment_type' => 'down'
                    )); 
                    
                    (PaymentLog::create(['amt' => $extra_seat_total +$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down1']));
                    
                    
                                     
                    (PaymentLog::create(['amt' => $invoice->downpayment_amount2,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down2']));
        
            Payment::where('booking_id', $BookingId)->update(['price' => $invoice->amount + $invoice->downpayment_amount2, 'payment_method' => 'down2']);
        }
         if ($payment_type == 'down3') //  && $no_of_seats==$input_seats
        {
            $price += $invoice->downpayment_amount3;
            Booking::where('id', $BookingId)
                ->update(
                    array(
                    
                        'remaining_amount' =>0,
                        'payment_type' => 'full'
                    ));
                
                    (PaymentLog::create(['amt' => $extra_seat_total+$invoice->downpayment_amount1,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down1']));
                    
                    
                                     
                    (PaymentLog::create(['amt' => $invoice->downpayment_amount2,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down2']));
        
                    
                    
                                     
                    (PaymentLog::create(['amt' => $invoice->downpayment_amount3,'result'=>'CAPTURED', 'event_id' => $invoice->event_id,'user_id' => $userid , 'payment_method' => 'down3']));

        }
        
        
        
         $booking = Booking::where('id',$invoice->id)->first();
        $event =  Ceremony::where('id',$booking->event_id)
            ->first();
            if($event)
            {
        $totalseats = Booking::where('event_id',$booking->event_id)->sum('no_of_seats');
     
            $total_amount=$event->ceremony_price + ($booking->no_of_seats-$event->free_seats)*$event->price;

        if($booking->downpayment_amount1 != $event->minimum_downpayment_amount){
            $booking->downpayment_amount1 = $event->minimum_downpayment_amount;
        }
        if($booking->downpayment_amount2 != $event->downpayment_amount2){
            $booking->downpayment_amount2 = $event->downpayment_amount2;
        }
        if($booking->downpayment_amount3 != $event->downpayment_amount3){
            $booking->downpayment_amount3 = $event->downpayment_amount3;
        }
        if($booking->ceremony_price != $event->ceremony_price){
            $booking->ceremony_price = $event->ceremony_price;
        }
        
        $booking->total_amount = $total_amount ;
        $paymentLogAmt = PaymentLog::where(
            'user_id',$booking->user_id)->where(
            'event_id',$booking->event_id)->where('result','CAPTURED')->sum('amt');
        
        $booking->remaining_amount=   $event->ceremony_price-$paymentLogAmt;
        $booking->save();
        }
        
        return $response;
    }
    public function checkSeatAvailabilityForEvent($eventid, $seats)
    {
        $ceremonies = Ceremony::with('booking')->where('id', $eventid)
            ->select('id', 'total_seats')->get();
        return ($ceremonies);
        foreach ($ceremonies as $key => $value) {
            if (!empty($value->booking)) {
                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));
                $rms = $value->total_seats - $sum;
                $value->remaining_seats = $rms < 0 ? 0 : $rms;
            } else {
                $value->remaining_seats = 0;
            }
        }
        if ($seats > $value->remaining_seats) {
            return false;
        } else {
            return true;
        }
    }
    public function recalcBooking(Request $request)
    {
        // $bookings = Booking::all();
        // foreach($bookings as $innerbook)
        // {
        $booking = Booking::where('id',$request->id)->first();
        $event =  Ceremony::where('id',$booking->event_id)
            ->first();
            if($event)
            {
        $totalseats = Booking::where('event_id',$booking->event_id)->sum('no_of_seats');
     
            $total_amount=$event->ceremony_price + ($booking->no_of_seats-$event->free_seats)*$event->price;

        if($booking->downpayment_amount1 != $event->minimum_downpayment_amount){
            $booking->downpayment_amount1 = $event->minimum_downpayment_amount;
        }
        if($booking->downpayment_amount2 != $event->downpayment_amount2){
            $booking->downpayment_amount2 = $event->downpayment_amount2;
        }
        if($booking->downpayment_amount3 != $event->downpayment_amount3){
            $booking->downpayment_amount3 = $event->downpayment_amount3;
        }
        if($booking->ceremony_price != $event->ceremony_price){
            $booking->ceremony_price = $event->ceremony_price;
        }
        $booking->total_amount = $total_amount ;
        
        $paymentLogAmt = PaymentLog::where(
            'user_id',$booking->user_id)->where(
            'event_id',$booking->event_id)->where('result','CAPTURED')->sum('amt');
        
        $booking->remaining_amount=   $event->ceremony_price-$paymentLogAmt;
        $booking->save();
        // }
        }
        // return  $booking->total_amount  ;
        
    }
    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $booking = Booking::where('id',$id)->first();
         PaymentLog::where(
            'user_id',$booking->user_id)->where(
            'event_id',$booking->event_id)->delete();
    
        return $this->crud->delete($id);
    }
}