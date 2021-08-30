<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailTemplate
 * 
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $action
 * @property string $body
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class EmailTemplate extends Model
{
    use CrudTrait;

    protected $table = 'email_templates';

	protected $fillable = [
		'name',
		'subject',
		'action',
		'body'
	];
}
