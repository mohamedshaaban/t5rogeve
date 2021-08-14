<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ad
 * 
 * @property int $id
 * @property string|null $ad_name
 * @property string|null $ad_image
 * @property string|null $ad_link
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $ads_from
 * @property Carbon $ads_to
 *
 * @package App\Models
 */
class Ad extends Model
{
	protected $table = 'ads';

	protected $casts = [
		'status' => 'int'
	];

	protected $dates = [
		'ads_from',
		'ads_to'
	];

	protected $fillable = [
		'ad_name',
		'ad_image',
		'ad_link',
		'status',
		'ads_from',
		'ads_to'
	];
}
