<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faculty
 * 
 * @property int $id
 * @property string $full_name
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Faculty extends Model
{
    use CrudTrait;
	protected $table = 'faculty';

	protected $fillable = [
		'full_name',
		'status'
	];
	protected $appends = ['statustext'];

	public function getStatustextAttribute($crud = false)
    {
        if(isset($this->attributes['status'])&&$this->attributes['status']==1)
        {
            return '<span class="badge badge-success">'.trans('admin.active').'</span>';
        }
        return '<span class="badge badge-danger">'.trans('admin.not_active').'</span>';
    }
	public function openStatus($crud = false)
    {
        if($this->status)
        {
            return '<span class="badge badge-success">'.trans('admin.active').'</span>';
        }
        return '<span class="badge badge-danger">'.trans('admin.not_active').'</span>';
    }
}
