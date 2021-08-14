<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'first_name'=>$this->first_name ?? '',
            'middle_name'=>$this->middle_name ?? '',
            'last_name'=>$this->last_name ?? '',
            'email'=>$this->email ?? '',
            'username'=>$this->username ?? '',
            'full_name'=>$this->full_name ?? '',
            'grandfather_name'=>$this->grandfather_name ?? '',
            'father_name'=>$this->father_name ?? '',
            'family_name'=>$this->family_name ?? '',
            'slug'=>$this->slug ?? '',
             'civil_id'=>$this->civil_id ?? '',
            'faulty'=>$this->faulty ?? '',
            'image'=>$this->image ?? '',

            'address'=>$this->address ?? '',
            'gender'=>$this->gender ?? '',
             'phone'=>$this->phone ?? '',
             'is_payment_complete'=>$this->is_payment_complete ?? '',
            'is_verified'=>$this->is_verified ?? '',
            'date_of_birth'=>$this->date_of_birth ?? '',
            'active'=>$this->active ?? '',

        
        ];
    }
}
