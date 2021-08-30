<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemImage
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $title
 * @property string|null $type
 * @property int $is_active
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class SystemImage extends Model
{
	protected $table = 'system_images';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'name',
		'title',
		'type',
		'is_active',
		'created',
		'modified'
	];
}
