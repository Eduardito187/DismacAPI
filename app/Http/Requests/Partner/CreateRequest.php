<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param  Request  $request
     * @return bool
     */
    public function authorize(Request  $request)
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "partner" => array(
                "name" => "required",
                "domain" => "required",
                "email" => "required",
                "nit" => "required",
                "razon_social" => "required",
                "legal_representative" => "required",
                "address" => array(
                    "id_municipality" => "required",
                    "id_country" => "required",
                    "id_city" => "required",
                    "address_extra" => array(
                        "address" => "required",
                        "extra" => "required"
                    ),
                    "localization" => array(
                        "latitud" => "required",
                        "longitud" => "required",
                    )
                )
            ),
            "account" => array(
                "name" => "required",
                "email" => "required",
                "username" => "required",
                "password" => "required"
            )
        ];
    }
}
