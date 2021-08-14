<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteAddress
 * 
 * @property int $id
 * @property string $content
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class SiteAddress extends Model
{
	protected $table = 'site_addresses';

	protected $fillable = [
		'content'
	];
}
