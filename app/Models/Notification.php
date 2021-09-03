<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * 
 * @property int $id
 * @property int|null $userid
 * @property int|null $eventid
 * @property int|null $alluser
 * @property string $notification
 * @property string|null $link
 * @property int|null $ceremony_for
 * @property string|null $payment_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Notification extends Model
{
    use CrudTrait;

    protected $table = 'notifications';

	protected $casts = [
		'userid' => 'int',
		'eventid' => 'int',
		'alluser' => 'int',
		'ceremony_for' => 'int'
	];

	protected $appends =['ceremonyfor'];
	protected $fillable = [
		'userid',
		'eventid',
		'alluser',
		'notification',
		'link',
		'for',
		'ceremony_for',
		'payment_type'
	];

    public function user() {
        return $this->belongsTo(Customer::class,'userid');
    }

    public function ceremony() {

        return $this->belongsTo(Ceremony::class,'eventid');
    }
    public function getCeremonyforAttribute()
    {
        if(isset($this->attributes['ceremonyfor']))
        {
            return 'Cancaled';
        }
        return 'Not Cancaled';
    }
}
