<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Block
 * 
 * @property int $id
 * @property string|null $page_name
 * @property string|null $page
 * @property string|null $block_name
 * @property string|null $block
 * @property string|null $description
 * @property string|null $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Block extends Model
{
	protected $table = 'blocks';

	protected $fillable = [
		'page_name',
		'page',
		'block_name',
		'block',
		'description',
		'image'
	];
}
