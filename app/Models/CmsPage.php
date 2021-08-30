<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CmsPage
 * 
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string $body
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $slug
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class CmsPage extends Model
{
    use CrudTrait;
	protected $table = 'cms_pages';

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'name',
		'title',
		'body',
		'meta_title',
		'meta_description',
		'meta_keywords',
		'slug',
		'is_active'
	];
}
