<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * 
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $title
 * @property string $description
 * @property string $input_type
 * @property bool $editable
 * @property int|null $weight
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class Setting extends Model
{
	protected $table = 'settings';

	protected $casts = [
		'editable' => 'bool',
		'weight' => 'int'
	];

	protected $fillable = [
		'key',
		'value',
		'title',
		'description',
		'input_type',
		'editable',
		'weight'
	];
}
