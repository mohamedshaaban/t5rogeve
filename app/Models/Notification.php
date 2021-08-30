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

	protected $fillable = [
		'userid',
		'eventid',
		'alluser',
		'notification',
		'link',
		'ceremony_for',
		'payment_type'
	];
}
