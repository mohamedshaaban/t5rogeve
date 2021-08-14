<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Meta
 * 
 * @property int $id
 * @property string $page_id
 * @property string $meta_title
 * @property string $slug
 * @property string $meta_keyword
 * @property string $description
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Meta extends Model
{
	protected $table = 'metas';

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'page_id',
		'meta_title',
		'slug',
		'meta_keyword',
		'description',
		'is_active'
	];
}
