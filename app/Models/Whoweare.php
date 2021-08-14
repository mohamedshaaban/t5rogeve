<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Whoweare
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Whoweare extends Model
{
	protected $table = 'whoweare';

	protected $fillable = [
		'title',
		'image'
	];
}
