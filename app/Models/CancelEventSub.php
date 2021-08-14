<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CancelEventSub
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $event_id
 * @property int|null $book_id
 * @property int|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class CancelEventSub extends Model
{
	protected $table = 'cancel_event_sub';

	protected $casts = [
		'user_id' => 'int',
		'event_id' => 'int',
		'book_id' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'event_id',
		'book_id',
		'status'
	];
}
