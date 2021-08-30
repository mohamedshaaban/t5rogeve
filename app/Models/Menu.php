<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $id
 * @property int $parent_id
 * @property string $type
 * @property string $menu_name
 * @property string $url
 * @property int $menu_order
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Menu extends Model
{
	protected $table = 'menus';

	protected $casts = [
		'parent_id' => 'int',
		'menu_order' => 'int',
		'is_active' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'type',
		'menu_name',
		'url',
		'menu_order',
		'is_active'
	];
}
