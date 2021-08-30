<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersLogin
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class UsersLogin extends Model
{
	protected $table = 'users_login';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id'
	];
}
