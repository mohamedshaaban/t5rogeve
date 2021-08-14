<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BlogLike
 * 
 * @property int $user_id
 * @property int $blog_id
 * @property Carbon $created
 *
 * @package App\Models
 */
class BlogLike extends Model
{
	protected $table = 'blog_likes';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'blog_id' => 'int'
	];

	protected $dates = [
		'created'
	];

	protected $fillable = [
		'created'
	];
}
