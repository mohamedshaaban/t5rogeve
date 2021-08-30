<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteAddressesDe
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string $content
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class SiteAddressesDe extends Model
{
	protected $table = 'site_addresses_des';

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'content'
	];
}
