<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KnetTransactions2
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $error_text
 * @property string|null $paymentid
 * @property bool|null $paid
 * @property string|null $result
 * @property string|null $auth
 * @property string|null $avr
 * @property string|null $ref
 * @property string|null $tranid
 * @property string|null $postdate
 * @property string|null $udf1
 * @property string|null $udf2
 * @property string|null $udf3
 * @property string|null $udf4
 * @property string|null $udf5
 * @property string|null $amt
 * @property string|null $error
 * @property int|null $auth_resp_code
 * @property string $trackid
 * @property bool $livemode
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 *
 * @package App\Models
 */
class KnetTransactions2 extends Model
{
	protected $table = 'knet_transactions2';

	protected $casts = [
		'user_id' => 'int',
		'paid' => 'bool',
		'auth_resp_code' => 'int',
		'livemode' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'error_text',
		'paymentid',
		'paid',
		'result',
		'auth',
		'avr',
		'ref',
		'tranid',
		'postdate',
		'udf1',
		'udf2',
		'udf3',
		'udf4',
		'udf5',
		'amt',
		'error',
		'auth_resp_code',
		'trackid',
		'livemode',
		'url'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class, 'user_id');
	}
}
