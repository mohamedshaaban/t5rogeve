<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sponsorplatinum
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $link
 * @property int|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Sponsorplatinum extends Model
{
	protected $table = 'sponsorplatinum';

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'title',
		'image',
		'link',
		'status'
	];
}
