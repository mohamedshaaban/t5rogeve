<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BlogComment
 * 
 * @property int $id
 * @property int $blog_id
 * @property int $user_id
 * @property string|null $comment
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class BlogComment extends Model
{
	protected $table = 'blog_comments';
	public $timestamps = false;

	protected $casts = [
		'blog_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'blog_id',
		'user_id',
		'comment',
		'created',
		'modified'
	];
}
