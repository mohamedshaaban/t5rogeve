<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NewsletterSubscriber
 * 
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $name
 * @property int $status
 * @property int $is_verified
 * @property string|null $enc_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class NewsletterSubscriber extends Model
{
	protected $table = 'newsletter_subscribers';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'int',
		'is_verified' => 'int'
	];

	protected $fillable = [
		'user_id',
		'email',
		'name',
		'status',
		'is_verified',
		'enc_id'
	];
}
