<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemDocument
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $title
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class SystemDocument extends Model
{
	protected $table = 'system_documents';

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'name',
		'title',
		'is_active'
	];
}
