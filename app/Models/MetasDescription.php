<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MetasDescription
 * 
 * @property int $id
 * @property int $page_id
 * @property int $parent_id
 * @property int $language_id
 * @property string $meta_title
 * @property string $meta_keyword
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MetasDescription extends Model
{
	protected $table = 'metas_descriptions';

	protected $casts = [
		'page_id' => 'int',
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'page_id',
		'parent_id',
		'language_id',
		'meta_title',
		'meta_keyword',
		'description'
	];
}
