<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Textsetting
 * 
 * @property int $id
 * @property int $language_id
 * @property string|null $key_value
 * @property string|null $value
 * @property int $type
 * @property int $js_constant_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Textsetting extends Model
{
	protected $table = 'textsettings';

	protected $casts = [
		'language_id' => 'int',
		'type' => 'int',
		'js_constant_type' => 'int'
	];

	protected $fillable = [
		'language_id',
		'key_value',
		'value',
		'type',
		'js_constant_type'
	];
}
