<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ceremony
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $description
 * @property int|null $total_seats
 * @property int|null $hide_seats
 * @property int $hide_UsersSeatsN
 * @property int $number_of_students
 * @property int|null $remaining_seats
 * @property float|null $price
 * @property float $ceremony_price
 * @property int $free_seats
 * @property string|null $image
 * @property string|null $imagemain
 * @property string|null $imagedes
 * @property int $status
 * @property string $faculty
 * @property string|null $hashtag
 * @property float $minimum_downpayment_amount
 * @property float|null $downpayment_amount2
 * @property Carbon|null $Name_Ex_Date
 * @property Carbon|null $RobSize_Ex_Date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $date
 * @property string $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $terms
 * @property string|null $imageterm
 * @property int $ceremony_for
 *
 * @package App\Models
 */
class Ceremony extends Model
{
	protected $table = 'ceremony';

	protected $casts = [
		'total_seats' => 'int',
		'hide_seats' => 'int',
		'hide_UsersSeatsN' => 'int',
		'number_of_students' => 'int',
		'remaining_seats' => 'int',
		'price' => 'float',
		'ceremony_price' => 'float',
		'free_seats' => 'int',
		'status' => 'int',
		'minimum_downpayment_amount' => 'float',
		'downpayment_amount2' => 'float',
		'latitude' => 'float',
		'longitude' => 'float',
		'ceremony_for' => 'int'
	];

	protected $dates = [
		'Name_Ex_Date',
		'RobSize_Ex_Date',
		'date'
	];

	protected $fillable = [
		'name',
		'code',
		'description',
		'total_seats',
		'hide_seats',
		'hide_UsersSeatsN',
		'number_of_students',
		'remaining_seats',
		'price',
		'ceremony_price',
		'free_seats',
		'image',
		'imagemain',
		'imagedes',
		'status',
		'faculty',
		'hashtag',
		'minimum_downpayment_amount',
		'downpayment_amount2',
		'Name_Ex_Date',
		'RobSize_Ex_Date',
		'date',
		'address',
		'latitude',
		'longitude',
		'terms',
		'imageterm',
		'ceremony_for'
	];
}
