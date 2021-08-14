<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 * 
 * @property int $id
 * @property int $state_id
 * @property int $country_id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $zipcode
 * @property int $status
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class City extends Model
{
	protected $table = 'cities';
	public $timestamps = false;

	protected $casts = [
		'state_id' => 'int',
		'country_id' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'state_id',
		'country_id',
		'name',
		'slug',
		'zipcode',
		'status',
		'created',
		'modified'
	];
}
