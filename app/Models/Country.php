<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $country_iso_code
 * @property string|null $country_code
 * @property int $country_order
 * @property int $status
 * @property int $is_default
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Country extends Model
{
    use CrudTrait;
	protected $table = 'countries';

	protected $casts = [
		'country_order' => 'int',
		'status' => 'int',
		'is_default' => 'int'
	];

	protected $fillable = [
		'name',
		'slug',
		'country_iso_code',
		'country_code',
		'country_order',
		'status',
		'is_default'
	];
}
