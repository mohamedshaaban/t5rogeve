<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailAction
 * 
 * @property int $id
 * @property string $action
 * @property string $options
 *
 * @package App\Models
 */
class EmailAction extends Model
{
	protected $table = 'email_actions';
	public $timestamps = false;

	protected $fillable = [
		'action',
		'options'
	];
}
