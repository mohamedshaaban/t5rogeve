@php

    $events = \App\Models\Ceremony::OrderBy('id','DESC')->get();

@endphp

     <div class="col-sm-6 col-lg-2">
            <select name="event_id" style="width: 100%" data-init-function="bpFieldInitSelect2Element" data-language=""
                    class="form-control filterevent-class select2-hidden-accessible"
                    onchange="calcEvent(this.value)"
                    data-select2-id="3" tabindex="-1" aria-hidden="true" data-initialized="true">
                <option value="" selected>{{ trans('admin.Please Select') }}</option>

@foreach($events as $event )
                <option value="{{$event->id}}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card border-0 text-white bg-primary">
            <div class="card-body">
                <div class="text-value" id="eveDetBookSeats">0</div>

                <div>{{ trans('admin.booked seats') }} </div>

                <div class="progress progress-white progress-xs my-2">
                    <div class="progress-bar" role="progressbar" style="width: 1052.1%" aria-valuenow="1052.1" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

            </div>

        </div>
    </div>

    <div class="col-sm-6 col-lg-2">
        <div class="card border-0 text-white bg-success">
            <div class="card-body">
                <div class="text-value" id="eveDetAmtFull">0</div>

                <div>{{ trans('admin.amount of full payment') }}</div>

                <div class="progress progress-white progress-xs my-2">
                    <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

            </div>

        </div>
    </div>

    <div class="col-sm-6 col-lg-2">
        <div class="card border-0 text-white bg-warning">
            <div class="card-body">
                <div class="text-value" id="eveDetAmtDwnPay">0 </div>

                <div>{{ trans('admin.amount of down payment') }}</div>


            </div>

        </div>
    </div>

    <div class="col-sm-6 col-lg-2">
        <div class="card border-0 text-white bg-dark">
            <div class="card-body">
                <div class="text-value" id="eveDetAmtRem">0</div>

                <div>{{ trans('admin.amount of remaining amount') }} </div>

                <div class="progress progress-white progress-xs my-2">
                    <div class="progress-bar" role="progressbar" style="width: 124%" aria-valuenow="124" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-sm-6 col-lg-2">
        <div class="card border-0 text-white bg-dark">
            <div class="card-body">
                <div class="text-value" id="eveDetRegUser">0</div>

                <div>{{ trans('admin.registered users') }}</div>

                <div class="progress progress-white progress-xs my-2">
                    <div class="progress-bar" role="progressbar" style="width: 124%" aria-valuenow="124" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

            </div>

        </div>


</div>