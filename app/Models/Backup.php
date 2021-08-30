<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Backup
 * 
 * @property int $id
 * @property string|null $file
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class Backup extends Model
{
	protected $table = 'backups';
	public $timestamps = false;

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'file',
		'created',
		'modified'
	];
}
