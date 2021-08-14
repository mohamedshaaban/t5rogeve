<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faculty
 * 
 * @property int $id
 * @property string $full_name
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Faculty extends Model
{
	protected $table = 'faculty';

	protected $fillable = [
		'full_name',
		'status'
	];
}
