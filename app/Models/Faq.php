<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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
    use CrudTrait;
	protected $table = 'faqs';

	protected $appends=['isactive'];
	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'question',
		'answer',
		'is_active'
	];
    public function getIsactiveAttribute()
    {
        if($this->attributes['is_active'])
        {
            return 'Active';
        }
        return 'Not Active';
    }
}
