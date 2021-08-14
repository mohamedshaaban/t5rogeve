<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeviceInfo
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $device_id
 * @property string|null $device_type
 * @property string|null $device_token
 *
 * @package App\Models
 */
class DeviceInfo extends Model
{
	protected $table = 'device_info';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $hidden = [
		'device_token'
	];

	protected $fillable = [
		'user_id',
		'device_id',
		'device_type',
		'device_token'
	];
}
