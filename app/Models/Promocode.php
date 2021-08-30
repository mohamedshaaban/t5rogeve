<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Promocode
 * 
 * @property int $id
 * @property string|null $code
 * @property string|null $discount
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $date_from
 * @property Carbon $date_to
 *
 * @package App\Models
 */
class Promocode extends Model
{
	protected $table = 'promocodes';

	protected $casts = [
		'status' => 'int'
	];

	protected $dates = [
		'date_from',
		'date_to'
	];

	protected $fillable = [
		'code',
		'discount',
		'status',
		'date_from',
		'date_to'
	];
}
