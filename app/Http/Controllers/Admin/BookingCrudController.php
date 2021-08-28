<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Booking;
use App\Models\Cars;
use App\Models\Ceremony;
use App\Models\Faculty;
use App\Models\Payment;
use App\Models\PaymentLog;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class BookingCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Booking::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/booking');
        CRUD::setEntityNameStrings('booking', 'booking');
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

        $this->crud->addColumn([ // Text
            'name' => 'no_of_seats',
            'label' => 'Seats Book',
         ]);
        $this->crud->addColumn([ // Text
            'name' => 'amount',
            'label' => 'Amount',
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'payment_type',
            'label' => 'Payment Type',
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'remaining_amount',
            'label' => 'Remaining Amount',
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'robe_size',
            'label' => 'Robe Size',
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
            'tab' => 'Texts',
            'data_source' => url("/admin/fetch/ceremony"), // url to controller search function (with /{id} should return model)
        ]);

        CRUD::addField([  // Select2
            'label' => ' Payment Type',
            'type' => 'select_from_array',
            'name' => 'payment_type', // the db column for the foreign key
            'tab' => 'Texts',
            'options'=> [
                'full' => 'full',
                'down2' => 'Down2',
                'down' => 'Down'
            ]]
        );

        CRUD::addField([ // Text
            'name' => 'freeseats',
            'label' => 'Free Seats',
            'type' => 'number',
            'tab' => 'Texts',

        ]);

        CRUD::addField([ // Text
            'name' => 'event_price',
            'label' => 'Event Price',
            'type' => 'number',
            'tab' => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name' => 'seats',
            'label' => ' No of Seats',
            'type' => 'number',
            'tab' => 'Texts',
        ]);

        CRUD::addField([  // Select2
                'label' => 'Robe Size',
                'type' => 'select2_from_array',
                'name' => 'robe_size', // the db column for the foreign key
                'tab' => 'Texts',
                'options'=> [
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
        CRUD::addField([ // Text
            'name' => 'amount',
            'label' => ' Minimum DownPayment Amount',
            'type' => 'number',
            'tab' => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name' => 'downpayment_amount2',
            'label' => 'Payment Amount 2',
            'type' => 'number',
            'tab' => 'Texts',
        ]);


        CRUD::addField([ // Text
            'name' => 'total_amount',
            'label' => 'Total Amount',
            'type' => 'number',
            'tab' => 'Texts',
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
    public function store()
    {
        $this->crud->unsetValidation(); // validation has already been run
        $form = backpack_form_input();
         $response = $this->traitStore();

        $invoice = Booking::find($this->crud->entry->id);
        $total = 0 ;


        //|||||||||| payment type == full ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||
        $freeseats=$invoice->freeseats;
        $event_price=$invoice->event_price;
        $amount=$invoice->amount;
        $input_seats=$invoice->seats;
        $payment_type = $invoice->payment_type;
        $seatPrice = $invoice->amountper;
        $seatsPrice = $input_seats * $seatPrice;
        $TotalPrice = $invoice->total_amount;

        if($payment_type=="down" || $payment_type=="down2")
        {
            if($freeseats>0)
            {

                $price=$amount;
                $totalseat=$input_seats+$freeseats;
            }
            else
            {
                $price=$amount;
                $totalseat=$input_seats;
            }
        }
        else
        {
            if($freeseats>0)
            {
                $price = $TotalPrice;
                $totalseat=$input_seats+$freeseats;
            }
            else
            {
                $price=$TotalPrice;
                $totalseat=$input_seats;
            }
        }



        $remaining_amount = 0;
        if($invoice->payment_type == 'down'){

            $remaining_amount = $event_price - $invoice->amount;
        }else if($invoice->payment_type == 'down2'){

            $remaining_amount = $event_price - $invoice->amount -  $invoice->downpayment_amount2;

        }
        $payValue = 0 ;
        if($payment_type=="down") {
            $payValue = $amount;
        }else if($payment_type=="down2") {
            $payValue = $amount + $invoice->downpayment_amount2;

        }else{
            $payValue = $price ;
        }
        $payment_arr = [
            'user_id' => $invoice->user_id,
            'ceremony_id' => $invoice->event_id,
            'booking_id' => $this->crud->entry->id,
            'status' => 1,
            'price' => $payValue,
            'payment_method' =>$invoice->payment_type,

        ];



        $pay_obj = new Payment();
        foreach($payment_arr as $key => $value){
            $pay_obj->$key = $value;
        }

        $payment_dtl = $pay_obj->save();

        //update booking field
        $val=
            Ceremony::where('id', $invoice->event_id)
            ->first();

        $totalseat_hwe= $val->remaining_seats;
        $remainseat=$totalseat_hwe-$totalseat;

        Ceremony::where('id',$invoice->event_id)->update(['remaining_seats' => $remainseat]);


        return $response;
    }
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $response = $this->traitUpdate();
        $invoice = Booking::find($this->crud->entry->id);

        $BookingId = $this->crud->entry->id;
        $input_seats = $invoice->seats;
        $price = $invoice->amount;


        $booking =	Booking::with('ceremony')->find($BookingId);
        $eventid = $booking->event_id;
        $userid = $booking->user_id;
        $ceremony_price = $invoice->event_price;
        $freeSeats =  $booking->ceremony->free_seats;
        $prev_amount = $booking->amount;
        $no_of_seats = $booking->no_of_seats;
        $seatPrice = $invoice->amountper;
        $robesize	= $invoice->robe_size;
        $payment_type = $invoice->payment_type;
        $remaining_amount = $ceremony_price - $price;
        $totalseat = $freeSeats + $input_seats;
        // dd($freeSeats,$no_of_seats,$totalseat);
        $seatsPrice = $input_seats * $seatPrice;
        $full_amount = $invoice->total_amount;


        //|||||||||| payment type == full ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||

        if($payment_type=='full') // && $no_of_seats<$input_seats
        {

            $remaining_seat=$input_seats-$no_of_seats;


            $diff_seats = 0;
            $new_total_seats = 0;

            // check if there is avalibale seats
            if($input_seats > $no_of_seats){


                if(!$this->checkSeatAvailabilityForEvent($eventid, $input_seats)){

                   return $response ;
                }

            }


            Booking::where('id',$invoice->id)
                ->update(
                    array(

                        // 'user_id'		=> $request->user_name'),
                        'event_id' 		=> $invoice->event_name,
                        'no_of_seats'	=> $totalseat,
                        'amount'		=> $full_amount,
                        'robe_size'          => $robesize,
                        'remaining_amount'		=> 0,
                        'payment_type' => 'full'
                    ));


            //update ceremony field
            $val= Ceremony::where('id', $eventid)
                ->first();

            $totalremainseat= $val->remaining_seats;
            $remainseat=$totalremainseat-$remaining_seat;

            Ceremony::where('id',$eventid)->update(['remaining_seats' => $remainseat]);

        }


        //|||||||||| payment type == down ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||


        if($payment_type=='down') //  && $no_of_seats==$input_seats
        {

            Booking::where('id',$BookingId)
                ->update(
                    array(
                        // 'event_id' 		=> $request->event_name'),
                        // 'user_id'		=> $request->user_name'),
                        'event_id' 		=> $invoice->event_name,
                        'no_of_seats'	=> $freeSeats,
                        'amount'		=> $price,
                        'robe_size'          => $robesize,
                        'remaining_amount'		=> $remaining_amount,
                        'payment_type' => 'down'
                    ));



            Payment::where('booking_id',$BookingId)->update(['price'=>$price,'payment_method'=>'down']);

            PaymentLog::where('user_id', '=', $userid)
                ->where('event_id', '=', $eventid)
                ->update(['amt'=>$price , 'event_id'=>$invoice->event_id]);


        }


        //|||||||||| payment type == down2 ||||||||||
        //||||||||||||||||||||||||||||||||||||||||||


        if($payment_type=='down2') //  && $no_of_seats==$input_seats
        {

            Booking::where('id',$BookingId)
                ->update(
                    array(
                        'event_id' 		=> $invoice->event_id,
                        'no_of_seats'	=> $freeSeats,
                        'amount'		=> $invoice->amount +  $invoice->downpayment_amount2,
                        'robe_size'          => $robesize,
                        'remaining_amount'		=> $ceremony_price - $invoice->amount -  $invoice->downpayment_amount2,
                        'payment_type' => 'down'
                    ));



            Payment::where('booking_id',$BookingId)->update(['price'=>$invoice->amount +  $invoice->downpayment_amount2,'payment_method'=>'down2']);



        }

return  $response;
    }
    public function checkSeatAvailabilityForEvent($eventid,$seats){
        $ceremonies = Ceremony::with('booking')->where('id',$eventid)
            ->select('id','total_seats')->get();

        return($ceremonies);

        foreach ($ceremonies as $key => $value) {

            if(!empty($value->booking)){
                $sum = array_sum(array_column($value->bookings->toArray(), 'no_of_seats'));

                $rms = $value->total_seats - $sum;

                $value->remaining_seats = $rms < 0 ? 0 : $rms;

            }else{
                $value->remaining_seats = 0;
            }
        }

        if($seats > $value->remaining_seats){
            return false;
        }else{
            return true;
        }

    }

}