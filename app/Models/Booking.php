<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
	protected $table = 'bookings';

	protected $casts = [
		'user_id' => 'int',
		'ceremony_id' => 'int',
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
		'ceremony_id',
		'price',
		'promocode_id',
		'discount',
		'final_price',
		'seats',
		'status',
		'slug'
	];
}
