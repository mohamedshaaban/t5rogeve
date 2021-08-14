<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CmsPageDescription
 * 
 * @property int $id
 * @property int $foreign_key
 * @property int $language_id
 * @property string $source_col_name
 * @property string $source_col_description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class CmsPageDescription extends Model
{
	protected $table = 'cms_page_descriptions';

	protected $casts = [
		'foreign_key' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'foreign_key',
		'language_id',
		'source_col_name',
		'source_col_description'
	];
}
