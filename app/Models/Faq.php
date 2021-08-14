<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faq
 * 
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property int $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Faq extends Model
{
	protected $table = 'faqs';

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'question',
		'answer',
		'is_active'
	];
}
