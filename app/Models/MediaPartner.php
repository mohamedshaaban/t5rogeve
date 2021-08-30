<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MediaPartner
 * 
 * @property int $id
 * @property string $title
 * @property string $logo
 * @property string $logo_color
 * @property int $order_by
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MediaPartner extends Model
{
	protected $table = 'media_partners';

	protected $casts = [
		'order_by' => 'int',
		'is_active' => 'int'
	];

	protected $fillable = [
		'title',
		'logo',
		'logo_color',
		'order_by',
		'is_active'
	];
}
