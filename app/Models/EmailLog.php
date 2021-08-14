<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailLog
 * 
 * @property int $id
 * @property string $email_to
 * @property string $email_from
 * @property string $email_type
 * @property string $subject
 * @property string $message
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class EmailLog extends Model
{
	protected $table = 'email_logs';
	public $timestamps = false;

	protected $fillable = [
		'email_to',
		'email_from',
		'email_type',
		'subject',
		'message'
	];
}
