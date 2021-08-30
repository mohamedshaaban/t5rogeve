<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimonial
 * 
 * @property int $id
 * @property string $client_name
 * @property string $comment
 * @property int $is_active
 * @property int $is_highlight
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Testimonial extends Model
{
	protected $table = 'testimonials';

	protected $casts = [
		'is_active' => 'int',
		'is_highlight' => 'int'
	];

	protected $fillable = [
		'client_name',
		'comment',
		'is_active',
		'is_highlight'
	];
}
