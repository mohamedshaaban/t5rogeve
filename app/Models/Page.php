<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Page
 * 
 * @property int $id
 * @property string $page_type
 * @property string $name
 * @property string $body
 * @property string $title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $slug
 * @property string $page_path
 * @property string $featured_image
 * @property int $status
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class Page extends Model
{
	protected $table = 'pages';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'page_type',
		'name',
		'body',
		'title',
		'meta_description',
		'meta_keywords',
		'slug',
		'page_path',
		'featured_image',
		'status',
		'created',
		'modified'
	];
}
