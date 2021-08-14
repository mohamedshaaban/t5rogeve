<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscriber
 * 
 * @property int $id
 * @property int $newsletter_subscriber_id
 * @property int $newsletter_id
 * @property Carbon $created
 * @property Carbon $modified
 *
 * @package App\Models
 */
class Subscriber extends Model
{
	protected $table = 'subscribers';
	public $timestamps = false;

	protected $casts = [
		'newsletter_subscriber_id' => 'int',
		'newsletter_id' => 'int'
	];

	protected $dates = [
		'created',
		'modified'
	];

	protected $fillable = [
		'newsletter_subscriber_id',
		'newsletter_id',
		'created',
		'modified'
	];
}
