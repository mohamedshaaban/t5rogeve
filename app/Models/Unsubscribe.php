<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Unsubscribe
 * 
 * @property int $id
 * @property int|null $event_id
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $third_name
 * @property string|null $family
 * @property string|null $phone
 * @property int|null $amount_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Unsubscribe extends Model
{
	protected $table = 'unsubscribe';

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
		'amount_paid'
	];
}
