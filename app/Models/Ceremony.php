<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

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
		'hide_additional_seats' => 'int',
		'hide_ad_events' => 'int',
		'hide_UsersSeatsN' => 'int',
		'number_of_students' => 'int',
		'remaining_seats' => 'int',
		'price' => 'float',
		'ceremony_price' => 'float',
		'free_seats' => 'int',
		'status' => 'int',
		'minimum_downpayment_amount' => 'float',
		'downpayment_amount2' => 'float',
		'downpayment_amount3' => 'float',
		'latitude' => 'float',
		'longitude' => 'float',
		'ceremony_for' => 'int'
	];

	protected $appends = ['nameexdate','robeexdate','nameexdateand','robeexdateand','statustext','numstudents','payment_type','remaining',
    'full_name',
'father_name',
'grandfather_name','admindate',
'family_name'];


	protected $fillable = [
		'name',
		'link_store',
		'link_store_image',
		'code',
		'description',
		'total_seats',
		'hideDate',
		'hide_seats',
		'hide_additional_seats',
		'hide_ad_events',
		'hide_UsersSeatsN',
		'number_of_students',
		'remaining_seats',
		'price',
		'ceremony_price',
		'free_seats',
		'image',
		'imagemain',
		'imagedes',
		'imageterm2',
		'status',
		'faculty',
		'hashtag',
		'minimum_downpayment_amount',
		'downpayment_amount2',
		'downpayment_amount3',
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
    public function facultyrela()
    {
        return $this->belongsTo(Faculty::class,'faculty');
    }
    public function setrobeExDateAttribute($value) {
        $this->attributes['RobSize_Ex_Date'] = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function setnameExDateAttribute($value) {
        $this->attributes['Name_Ex_Date'] = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
//    public function setNameExDateAttribute($value) {
//
//        dd(\Carbon\Carbon::parse($value));
//        $this->attributes['NameExDate'] = \Carbon\Carbon::parse($value);
//    }
    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "/public/uploads/folder_1/folder_2";

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
            $this->attributes[$attribute_name] = $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }
    public function setLinkStoreImageAttribute($value)
    {
        $attribute_name = "link_store_image";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "/public/uploads/folder_1/folder_2";

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
            $this->attributes[$attribute_name] = $filename;
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
        $destination_path = "/public/uploads/folder_1/folder_2";

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
            $this->attributes[$attribute_name] = $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }

    public function setImageterm2Attribute($value)
    {
        $attribute_name = "imageterm2";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        $destination_path = "/public/uploads/folder_1/folder_2";
        if ($value == null) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpeg', 90);
            $filename = md5($value . time()) . '.jpeg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }

    public function getLinkStoreImageAttribute()
    {
        if($this->attributes['hide_ad_events']){
            return '' ;
        }
        if(isset($this->attributes['link_store_image']))
        {
            return   $this->attributes['link_store_image'];
        }
        return  '';
    }
    public function getLinkStoreAttribute()
    {
        if($this->attributes['hide_ad_events']){
            return '' ;
        }
        return  $this->attributes['link_store'];
    }
    public function getPaymentTypeAttribute()
    {
        $user = Auth::guard('customers_api')->user();
        if(!$user)
        {
            return '';
        }
        $user_id = $user->id;
        $paymentLogAmt = PaymentLog::where(
            'user_id',$user_id)->where(
            'event_id',$this->attributes['id'])->where('result','CAPTURED')->first();
        return @$paymentLogAmt->payment_type ;
    }
    public function getRemainingAttribute()
    {
        $user = Auth::guard('customers_api')->user();
        if(!$user)
        {
            return '';
        }
        $user_id = $user->id;
        $paymentLogAmt = PaymentLog::where(
            'user_id',$user_id)->where(
            'event_id',$this->attributes['id'])->where('result','CAPTURED')->sum('amt');

        return ($this->attributes['ceremony_price']- $paymentLogAmt >= 0 )? ($this->attributes['ceremony_price']- $paymentLogAmt  ) : 0 ;
    }
    public function getRobeexdateandAttribute()
    {
        if(!isset($this->attributes['RobSize_Ex_Date']))
        {
            return Carbon::parse(@$this->attributes['date'])->format('Y-m-d H:i:s');
        }
        return Carbon::parse(@$this->attributes['RobSize_Ex_Date'])->format('Y-m-d H:i:s');

    }
    public function getRobeexdateAttribute()
    {
        if(!isset($this->attributes['RobSize_Ex_Date']))
        {
            return Carbon::parse(@$this->attributes['date'])->format('Y-m-d H:i:s');
        }
        return $this->attributes['RobSize_Ex_Date'];
    }
    public function getDateAttribute()
    {
        if(!isset($this->hideDate))
        {
            return $this->attributes['date'];
        }
        return '';
    }
    public function getAdminDateAttribute()
    {
        // dd($this->attributes['date']);
                if(isset($this->attributes['date']))
                {
                    return $this->attributes['date'];
                }
            return '';
    }

    public function getNameexdateAttribute()
    {
        if(!isset($this->attributes['Name_Ex_Date']))
        {
             return Carbon::parse(@$this->attributes['date'])->format('Y-m-d H:i:s');
        }
        return $this->attributes['Name_Ex_Date'];
    }
    public function getNameexdateandAttribute()
    {
        if(!isset($this->attributes['Name_Ex_Date']))
        {
            return Carbon::parse(@$this->attributes['date'])->format('Y-m-d H:i:s');
        }
        return Carbon::parse(@$this->attributes['Name_Ex_Date'])->format('Y-m-d H:i:s');

    }
    public function setImagedesAttribute($value)
    {
        $attribute_name = "imagedes";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "/public/uploads/folder_1/folder_2";

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
            $this->attributes[$attribute_name] =  $filename;
        } else {
            return $this->attributes[$attribute_name] = $value;
        }
    }
    public function getImageAttribute($value)
    {
//        return $value;
         if (strpos($value, 'uploads') !== false) {
            return asset($value);

        }

        return asset('uploads/folder_1/folder_2/' . $value);
    }
    public function getImageterm2Attribute($value)
    {
        if (strpos($value, 'http') !== false) {
            return (  $value);
        }

        return asset('uploads/folder_1/folder_2/' . $value);
    }
    public function getImagemainAttribute($value)
    {
         if (strpos($value, 'http') !== false) {
            return (  $value);
        }

        return asset('uploads/folder_1/folder_2/' . $value);
    }
    public function getNumstudentsAttribute()
    {


        return $this->booking()->count('id').'/'.$this->number_of_students;
    }

    public function getStatustextAttribute()
    {
        return '';
    }
    public function getImagedesAttribute($value)
    {
        if (strpos($value, 'http') !== false) {
            return (  $value);
        }
        if (strpos($value, 'uploads') !== false) {
            return asset($value);

        }
        return asset('uploads/folder_1/folder_2/' . $value);
    }
    public function booking(){
        return $this->hasMany(Booking::class,'event_id');
    }
    public function userbooking(){
        return $this->hasMany(Booking::class,'event_id');
    }
    public function poll(){
        return $this->hasMany(Poll::class,'eventid');
    }
    public function amenities()
    {
        return $this->belongsToMany(Amenities::class, 'event_amenities','event_id');
    }

    public function openStatus($crud = false)
    {
        if($this->status)
        {
            return '<span class="badge badge-success">'.trans('admin.active').'</span>';
        }
        return '<span class="badge badge-danger">'.trans('admin.not_active').'</span>';

    }
    public function getFullNameAttribute()
    {
        if(Auth::guard('customers_api')) {
            $user = Auth::guard('customers_api')->user();
            if(!$user){
                return '';
            }
            $user_id = $user->id;
            $bookingdetails = Booking::where('event_id', $this->attributes['id'])->where('user_id',$user_id)
                ->first();
            return  isset($bookingdetails->full_name)?$bookingdetails->full_name:'';
        }
        return '';
    }
    public function getFatherNameAttribute()
    {

        if(Auth::guard('customers_api')) {
            $user = Auth::guard('customers_api')->user();
            if(!$user){
                return '';
            }
            $user_id = $user->id;
            $bookingdetails = Booking::where('event_id', $this->attributes['id'])->where('user_id',$user_id)
                ->first();
            return  isset($bookingdetails->father_name)?$bookingdetails->father_name:'';
        }
        return '';

    }
    public function getGrandfatherNameAttribute()
    {

        if(Auth::guard('customers_api')) {
            $user = Auth::guard('customers_api')->user();
            if(!$user){
                return '';
            }
            $user_id = $user->id;
            $bookingdetails = Booking::where('event_id', $this->attributes['id'])->where('user_id',$user_id)
                ->first();
            return  isset($bookingdetails->grandfather_name)?$bookingdetails->grandfather_name:'';
        }
        return '';

    }
        
    public function getHideAdEventsAttribute()
    {
        
                return (int)$this->attributes['hide_ad_events'] ;
    }

    public function getRemainingSeatsAttribute()
    {
        $bookingseats = Booking::where('event_id', $this->attributes['id'])
                ->sum('no_of_seats');
                return $this->attributes['total_seats'] - $bookingseats;
    }
    public function getFamilyNameAttribute()
    {

        if(Auth::guard('customers_api')) {
            $user = Auth::guard('customers_api')->user();
            if(!$user){
                return '';
            }
            $user_id = $user->id;
            $bookingdetails = Booking::where('event_id', $this->attributes['id'])->where('user_id',$user_id)
                ->first();
            return  isset($bookingdetails->family_name)?$bookingdetails->family_name:'';
        }
        return '';

    }
        public function openGoogle($crud = false)
    {
        return  '<a class="btn btn-sm btn-link"  href="http://booking.acakw.com/admin/booking?event_id='.$this->id.'&event_id_text='.$this->name.'"  >'.trans('admin.Bookings').'</a>';
    }
}
