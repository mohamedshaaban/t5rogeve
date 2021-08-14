<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NewsletterTemplate
 * 
 * @property int $id
 * @property string $subject
 * @property string $body
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class NewsletterTemplate extends Model
{
	protected $table = 'newsletter_templates';

	protected $fillable = [
		'subject',
		'body'
	];
}
