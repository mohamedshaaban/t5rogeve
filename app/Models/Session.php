<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Session
 * 
 * @property int $id
 * @property int $user_id
 * @property string $session_token
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Session extends Model
{
	protected $table = 'sessions';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $hidden = [
		'session_token'
	];

	protected $fillable = [
		'user_id',
		'session_token'
	];
}
