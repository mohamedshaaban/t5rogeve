<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
    use CrudTrait;
	protected $table = 'contact_us';

	protected $appends = ['isreply','events'];
	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'name',
		'email',
		'subject',
		'reply',
		'mobile',
        'admin_user'
	];
    public function user() {
        return $this->belongsTo(Customer::class,'user_id');
    }
    public function admin() {
        return $this->belongsTo(User::class,'admin_user');
    }
    public function getEventsAttribute()
    {
        $eventIds = Booking::where('user_id',$this->user_id)->pluck('event_id')->toArray();
         $events = Ceremony::whereIn('id',$eventIds)->pluck('name')->toArray();
        return implode(',',$events);
    }
    public function getIsreplyAttribute()
    {
        if (isset($this->attributes['reply'])) {
            return true;
        }
        return false;
    }
}
