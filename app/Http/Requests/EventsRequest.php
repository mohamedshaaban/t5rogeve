<?php

namespace App\Http\Requests;

class EventsRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|min:3|max:255',
            'code'        => 'required|min:3|max:255',
            'image'        => 'required',
            'imagemain'        => 'required',
            'imagedes'        => 'required',
            'address'        => 'required|min:3|max:255',
            'latitude'        => 'required',
            'longitude'        => 'required',
            'ceremony_for'        => 'required',
//            'faculty'        => 'required',



        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
