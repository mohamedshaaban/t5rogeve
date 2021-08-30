<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Language
 * 
 * @property int $id
 * @property string $title
 * @property string|null $lang_code
 * @property string|null $folder_code
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Language extends Model
{
	protected $table = 'languages';

	protected $casts = [
		'active' => 'int'
	];

	protected $fillable = [
		'title',
		'lang_code',
		'folder_code',
		'active'
	];
}
