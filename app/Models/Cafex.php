<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cafex
 * 
 * @property int $id
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $third_name
 * @property string|null $family
 * @property string|null $phone
 * @property string|null $area
 * @property string|null $block
 * @property string|null $street
 * @property string|null $avenue
 * @property string|null $house
 * @property string|null $floor
 * @property string|null $flat
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Cafex extends Model
{
    use CrudTrait;
	protected $table = 'cafex';

	protected $fillable = [
		'first_name',
		'second_name',
		'third_name',
		'family',
		'phone',
		'area',
		'block',
		'street',
		'avenue',
		'house',
		'floor',
		'flat'
	];
}
