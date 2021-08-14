<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Visitor
 * 
 * @property int $id
 * @property string|null $session_id
 * @property int|null $visitor_id
 * @property string|null $visitor_type
 * @property string|null $visitor_name
 * @property int|null $visit_time
 * @property int|null $visitor_ip
 * @property string|null $country_name
 * @property string|null $city
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $browser_name
 * @property string|null $browser_version
 * @property string|null $view_pages
 * @property string|null $user_browser
 * @property string|null $user_browser_device
 * @property string|null $clicked_from
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Visitor extends Model
{
	protected $table = 'visitors';

	protected $casts = [
		'visitor_id' => 'int',
		'visit_time' => 'int',
		'visitor_ip' => 'int'
	];

	protected $fillable = [
		'session_id',
		'visitor_id',
		'visitor_type',
		'visitor_name',
		'visit_time',
		'visitor_ip',
		'country_name',
		'city',
		'latitude',
		'longitude',
		'browser_name',
		'browser_version',
		'view_pages',
		'user_browser',
		'user_browser_device',
		'clicked_from'
	];
}
