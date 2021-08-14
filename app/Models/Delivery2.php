<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Delivery2
 * 
 * @property int $id
 * @property int|null $event_id
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $third_name
 * @property string|null $family
 * @property string|null $phone
 * @property string|null $sphone
 * @property string|null $area
 * @property string|null $block
 * @property string|null $street
 * @property string|null $avenue
 * @property string|null $house
 * @property string|null $floor
 * @property string|null $flat
 * @property int|null $amount_paid
 * @property string|null $pay_status
 * @property string|null $paymentid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Delivery2 extends Model
{
	protected $table = 'delivery2';

	protected $casts = [
		'event_id' => 'int',
		'amount_paid' => 'int'
	];

	protected $fillable = [
		'event_id',
		'first_name',
		'second_name',
		'third_name',
		'family',
		'phone',
		'sphone',
		'area',
		'block',
		'street',
		'avenue',
		'house',
		'floor',
		'flat',
		'amount_paid',
		'pay_status',
		'paymentid'
	];
}
