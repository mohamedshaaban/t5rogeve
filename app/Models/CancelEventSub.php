<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
    use CrudTrait;
	protected $table = 'cancel_event_sub';
	protected $appends = ['statstitle'];

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
    public function ceremony() {

        return $this->belongsTo(Ceremony::class,'event_id');
    }

    public function user() {
        return $this->belongsTo(Customer::class,'user_id');
    }
    public function getStatstitleAttribute()
    {
        if($this->attributes['status'])
        {
            return 'Cancaled';
        }
        return 'Not Cancaled';
    }
}
