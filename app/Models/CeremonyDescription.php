<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CeremonyDescription
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string $name
 * @property string $description
 * @property string $address
 * @property float $latitude
 * @property float $longitude
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class CeremonyDescription extends Model
{
	protected $table = 'ceremony_description';

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int',
		'latitude' => 'float',
		'longitude' => 'float'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'name',
		'description',
		'address',
		'latitude',
		'longitude'
	];
}
