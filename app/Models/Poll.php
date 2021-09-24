<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Poll
 * 
 * @property int $id
 * @property string|null $question
 * @property Carbon|null $startDate
 * @property Carbon|null $endDate
 * @property int|null $eventid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Poll extends Model
{
    use CrudTrait;

    protected $table = 'polls';

	protected $casts = [
		'eventid' => 'int'
	];



	protected $fillable = [
		'question',
		'startDate',
		'endDate',
		'eventid',
        'polloption'
	];
    public function ceremony() {
//        if(backpack_user()->faculty_id!=0)
//        {
//            $eventIds = Ceremony::where('faculty',backpack_user()->faculty_id)->pluck('id')->toArray();
//            return $this->belongsTo(Ceremony::class,'eventid')->whereIn('eventid',$eventIds);
//
//        }

        return $this->belongsTo(Ceremony::class,'eventid');
    }
    public function polloption() {

        return $this->hasMany(PollOption::class,'poll_id');
    }



}
