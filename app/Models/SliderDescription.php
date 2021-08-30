<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SliderDescription
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string|null $slider_text
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class SliderDescription extends Model
{
	protected $table = 'slider_descriptions';

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'slider_text'
	];
}
