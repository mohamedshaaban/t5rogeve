<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DropdownManager
 * 
 * @property int $id
 * @property string|null $slug
 * @property string $dropdown_type
 * @property string $name
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class DropdownManager extends Model
{
	protected $table = 'dropdown_managers';

	protected $fillable = [
		'slug',
		'dropdown_type',
		'name',
		'image'
	];
}
