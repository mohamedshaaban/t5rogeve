<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TermsCondition
 * 
 * @property int $id
 * @property string $content
 * @property string|null $image
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class TermsCondition extends Model
{
	protected $table = 'terms_conditions';

	protected $fillable = [
		'content',
		'image'
	];
}
