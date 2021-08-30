<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WaysUse
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $link
 * @property int|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class WaysUse extends Model
{
    use CrudTrait;

    protected $table = 'ways_use';

	protected $casts = [
		'status' => 'int'
	];

	protected $appends =['statstitle'];
	protected $fillable = [
		'title',
		'link',
		'status'
	];
    public function getStatstitleAttribute()
    {
        if($this->attributes['status'])
        {
            return 'Active';
        }
        return 'Not Active';
    }

}
