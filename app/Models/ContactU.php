<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactU
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $email
 * @property string $subject
 * @property string|null $reply
 * @property string $mobile
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class ContactU extends Model
{
	protected $table = 'contact_us';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'name',
		'email',
		'subject',
		'reply',
		'mobile'
	];
}
