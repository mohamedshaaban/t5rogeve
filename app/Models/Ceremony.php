<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    use CrudTrait;

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

	protected $appends = ['nameexdate','robeexdate'];

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
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty2');
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/folder_1/folder_2";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }

    public function setImageMainAttribute($value)
    {
        $attribute_name = "imagemain";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/folder_1/folder_2";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }

    public function setImagetermAttribute($value)
    {
        $attribute_name = "imageterm";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/folder_1/folder_2";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }
    public function getRobeexdateAttribute()
    {
        if(!$this->attributes['RobSize_Ex_Date'])
        {
            return $this->attributes['date'];
        }
        return $this->attributes['RobSize_Ex_Date'];
    }

    public function getNameexdateAttribute()
    {
        if(!$this->attributes['Name_Ex_Date'])
        {
            return $this->attributes['date'];
        }
        return $this->attributes['Name_Ex_Date'];

    }
    public function setImagedesAttribute($value)
    {
        $attribute_name = "imagedes";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/folder_1/folder_2";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }
    public function getImageAttribute($value)
    {
//        return $value;

        if ($value != null && $value != '') {
            return asset('uploads/folder_1/folder_2/' . $value);
        }
        return $value;
    }
    public function getImagemainAttribute($value)
    {
//        return $value;

        if ($value != null && $value != '') {
            return asset('uploads/folder_1/folder_2/' . $value);
        }
        return $value;
    }

    public function getImagedesAttribute($value)
    {
//        return $value;

        if ($value != null && $value != '') {
            return asset('uploads/folder_1/folder_2/' . $value);
        }
        return $value;
    }
    public function booking(){
        return $this->hasMany(Booking::class, 'id','booking_id');
    }
}
