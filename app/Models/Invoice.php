<?php

namespace App\Models; 
use Eloquent;

class Invoice extends Eloquent
{
  //
  public $table = "payment_log";
  
  	public function ceremony() {
		
		return $this->belongsTo(Ceremony::class,'event_id');
	}
	
		public function user() {
		return $this->belongsTo(Customer::class,'user_id');
	}
	
		public function booking() {
		return $this->belongsTo('App\Models\Ceremony');
	}

	public function CeremonyDescription() {
		return $this->belongsTo('App\Models\Ceremony');
	}

  
}