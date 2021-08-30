<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FacultyDescription
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string $full_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class FacultyDescription extends Model
{
    use CrudTrait;

    protected $table = 'faculty_descriptions';

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'full_name'
	];
}
