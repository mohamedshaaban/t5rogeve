<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PollAnswered
 * 
 * @property int $id
 * @property int $poll_id
 * @property int $user_id
 * @property int $poll_options_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class PollAnswered extends Model
{
    use CrudTrait;

    protected $table = 'poll_answered';

	protected $casts = [
		'poll_id' => 'int',
		'user_id' => 'int',
		'poll_options_id' => 'int'
	];

	protected $fillable = [
		'poll_id',
		'user_id',
		'poll_options_id'
	];
}
