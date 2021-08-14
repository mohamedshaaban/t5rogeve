<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FaqDescription
 * 
 * @property int $id
 * @property int $parent_id
 * @property int $language_id
 * @property string $question
 * @property string $answer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class FaqDescription extends Model
{
	protected $table = 'faq_descriptions';

	protected $casts = [
		'parent_id' => 'int',
		'language_id' => 'int'
	];

	protected $fillable = [
		'parent_id',
		'language_id',
		'question',
		'answer'
	];
}
