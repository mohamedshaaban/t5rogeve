<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentLog4
 * 
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property string|null $booking_no
 * @property string|null $paymentid
 * @property string $result
 * @property string|null $auth
 * @property string|null $avr
 * @property string $ref
 * @property string $tranid
 * @property string|null $postdate
 * @property string $trackid
 * @property string $amt
 * @property string|null $authRespCode
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $marchant_id
 * @property string $card_type
 * @property int|null $invoic_id
 * @property string $phone
 *
 * @package App\Models
 */
class PaymentLog4 extends Model
{
	protected $table = 'payment_log4';

	protected $casts = [
		'user_id' => 'int',
		'event_id' => 'int',
		'marchant_id' => 'int',
		'invoic_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'event_id',
		'booking_no',
		'paymentid',
		'result',
		'auth',
		'avr',
		'ref',
		'tranid',
		'postdate',
		'trackid',
		'amt',
		'authRespCode',
		'marchant_id',
		'card_type',
		'invoic_id',
		'phone'
	];
}
