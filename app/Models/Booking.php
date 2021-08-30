<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string $booking_no
 * @property int|null $ceremony_id
 * @property float|null $price
 * @property int|null $promocode_id
 * @property float|null $discount
 * @property float|null $final_price
 * @property int $seats
 * @property int $status
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Booking extends Model
{
    use CrudTrait;
	protected $table = 'booking';

	protected $casts = [
		'user_id' => 'int',
		'event_id' => 'int',
		'price' => 'float',
		'promocode_id' => 'int',
		'discount' => 'float',
		'final_price' => 'float',
		'seats' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'booking_no',
		'event_id',
		'price',
		'promocode_id',
		'discount',
		'final_price',
		'seats',
		'status',
		'slug',
        'payment_type',
        'no_of_seats',
        'event_price',
        'freeseats',
        'amount',
        'ceremony_price',
        'downpayment_amount2',
        'robe_size',
        'total_amount',
        'remaining_amount'
	];
    public function ceremony() {

        return $this->belongsTo(Ceremony::class,'event_id');
    }


    public function ceremonyWithDescription() {
        $lang = session('lang');

        return $this->belongsTo(Ceremony::class,'event_id')->Join('ceremony_description', function($join) {
            $join->on('ceremony.id', '=', 'ceremony_description.parent_id');
        })->where("language_id",$lang)->select('ceremony.id','ceremony_description.name','ceremony_description.description','total_seats','remaining_seats','price','ceremony_price','free_seats','image','status','faculty','minimum_downpayment_amount','ceremony_description.created_at','ceremony_description.updated_at','date','ceremony_description.address','ceremony_description.latitude','ceremony_description.longitude','ceremony_for');

    }

    public function user() {
        return $this->belongsTo(Customer::class,'user_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'booking_id');
    }

}
