<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $ceremony_id
 * @property int|null $booking_id
 * @property float|null $price
 * @property string|null $transaction_no
 * @property string $payment_method
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Payment extends Model
{
	protected $table = 'payments';

	protected $casts = [
		'user_id' => 'int',
		'ceremony_id' => 'int',
		'booking_id' => 'int',
		'price' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'ceremony_id',
		'booking_id',
		'price',
		'transaction_no',
		'payment_method',
		'status'
	];
}
