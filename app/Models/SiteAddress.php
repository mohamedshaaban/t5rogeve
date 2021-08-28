<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
    use CrudTrait;
	protected $table = 'site_addresses';

	protected $fillable = [
		'content'
	];
}
