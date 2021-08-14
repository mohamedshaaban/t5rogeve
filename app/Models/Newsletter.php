<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Newsletter
 * 
 * @property int $id
 * @property int $newsletter_template_id
 * @property string $subject
 * @property string $body
 * @property int $status
 * @property Carbon $scheduled_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Newsletter extends Model
{
	protected $table = 'newsletters';

	protected $casts = [
		'newsletter_template_id' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'scheduled_time'
	];

	protected $fillable = [
		'newsletter_template_id',
		'subject',
		'body',
		'status',
		'scheduled_time'
	];
}
