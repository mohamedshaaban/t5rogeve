<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BlockDescription
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string|null $description
 *
 * @package App\Models
 */
class BlockDescription extends Model
{
	protected $table = 'block_descriptions';
	public $timestamps = false;

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'description'
	];
}
