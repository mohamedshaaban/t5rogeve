<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Slider
 * 
 * @property int $id
 * @property string|null $slider_text
 * @property string|null $slider_image
 * @property int $slider_order
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Slider extends Model
{
	protected $table = 'sliders';

	protected $casts = [
		'slider_order' => 'int',
		'is_active' => 'int'
	];

	protected $fillable = [
		'slider_text',
		'slider_image',
		'slider_order',
		'is_active'
	];
}
