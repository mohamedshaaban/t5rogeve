<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class Customer
 * 
 * @property int $id
 * @property string|null $email
 * @property string|null $username
 * @property string $full_name
 * @property string $grandfather_name
 * @property string $father_name
 * @property string $family_name
 * @property string|null $slug
 * @property string|null $password
 * @property int $user_role_id
 * @property string|null $civil_id
 * @property string|null $faulty
 * @property string|null $image
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $address
 * @property string $gender
 * @property int|null $country
 * @property int|null $region
 * @property int|null $city
 * @property int $phone
 * @property int|null $zip_code
 * @property int $is_payment_complete
 * @property int $is_verified
 * @property int|null $otp
 * @property string|null $remember_token
 * @property string|null $type
 * @property bool $active
 * @property string|null $admin
 * @property int|null $role_id
 * @property int $is_deleted
 * @property string|null $validate_string
 * @property string|null $forgot_password_validate_string
 * @property string|null $password_temp
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|KnetTransactions2[] $knet_transactions2s
 *
 * @package App\Models
 */
class Customer extends Authenticatable
{
    use CrudTrait;
    use HasApiTokens, Notifiable;

    use SoftDeletes;
	protected $table = 'customers';

	protected $casts = [
		'user_role_id' => 'int',
		'country' => 'int',
		'region' => 'int',
		'city' => 'int',
		'phone' => 'int',
		'zip_code' => 'int',
		'is_payment_complete' => 'int',
		'is_verified' => 'int',
		'otp' => 'int',
		'active' => 'bool',
		'role_id' => 'int',
		'is_deleted' => 'int'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $appends = ['all_name'];
	protected $fillable = [
		'email',
		'username',
		'full_name',
		'grandfather_name',
		'father_name',
		'family_name',
		'slug',
		'password',
		'user_role_id',
		'date_of_birth',
		'civil_id',
		'faulty',
		'image',
		'first_name',
		'middle_name',
		'last_name',
		'address',
		'gender',
		'country',
		'region',
		'city',
		'phone',
		'zip_code',
		'is_payment_complete',
		'is_verified',
		'otp',
		'remember_token',
		'type',
		'active',
		'admin',
		'role_id',
		'is_deleted',
		'validate_string',
		'forgot_password_validate_string',
		'password_temp',
        'image'
	];

	public function knet_transactions2s()
	{
		return $this->hasMany(KnetTransactions2::class, 'user_id');
	}
    public function getAllNameAttribute()
    {
            return $this->full_name.' ' .$this->father_name.' '.$this->grandfather_name.' '.$this->family_name;

    }
    public function getImageAttribute()
    {
        if($this->attributes['image'])
        {
            return asset('uploads/'.$this->attributes['image']);
        }
        return asset('uploads/default.png');


    }

}
