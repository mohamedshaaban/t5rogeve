<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Blog
 * 
 * @property int $id
 * @property string|null $slug
 * @property string|null $name
 * @property string|null $image
 * @property string|null $description
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Blog extends Model
{
	protected $table = 'blogs';

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'slug',
		'name',
		'image',
		'description',
		'is_active'
	];
}
