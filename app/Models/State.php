<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class State
 * 
 * @property int $id
 * @property int $country_id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $state_code
 * @property int $status
 * @property int $is_default
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class State extends Model
{
	protected $table = 'states';
	public $timestamps = false;

	protected $casts = [
		'country_id' => 'int',
		'status' => 'int',
		'is_default' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'country_id',
		'name',
		'slug',
		'state_code',
		'status',
		'is_default',
		'created',
		'modified'
	];
}
