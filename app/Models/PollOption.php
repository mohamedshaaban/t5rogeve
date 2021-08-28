<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PollOption
 * 
 * @property int $id
 * @property int $poll_id
 * @property string|null $answer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class PollOption extends Model
{
    use CrudTrait;

    protected $table = 'poll_options';

	protected $casts = [
		'poll_id' => 'int'
	];

	protected $fillable = [
		'poll_id',
		'answer'
	];
    public function poll()
    {
        return $this->belongsTo(\App\Models\Poll::class, 'poll_id');
    }
}
