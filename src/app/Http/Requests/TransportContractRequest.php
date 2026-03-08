<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract.vendor_name'      => 'required|string|max:255',
            'contract.vendor_city'      => 'required|string|max:255',
            'contract.vendor_country'   => 'required|string|max:255',
            'contract.pic_name'         => 'required|string|max:255',
            'contract.pic_phone'        => 'nullable|string|max:50',
            'contract.pic_email'        => 'nullable|email|max:255',
            'contract.price_category'   => 'required|in:FIT,GIT',
            'contract.currency'         => 'required|string|max:10',
            'contract.valid_from'       => 'required|date',
            'contract.valid_until'      => 'required|date|after:contract.valid_from',
            'contract.notes'            => 'nullable|string',

            'vehicles'                          => 'nullable|array',
            'vehicles.*.vehicle_name'           => 'required|string|max:255',
            'vehicles.*.category'               => 'required|in:car,van,bus',
            'vehicles.*.capacity'               => 'nullable|integer|min:1',
            'vehicles.*.brand'                  => 'nullable|string|max:100',
            'vehicles.*.notes'                  => 'nullable|string',
            'vehicles.*.rates'                  => 'nullable|array',
            'vehicles.*.rates.*.route_name'     => 'required|string|max:255',
            'vehicles.*.rates.*.route_type'     => 'required|in:airport_transfer,half_day,full_day,half_day_dinner,full_day_dinner,overnight,extra_hour',
            'vehicles.*.rates.*.duration'       => 'nullable|string|max:50',
            'vehicles.*.rates.*.price'          => 'nullable|numeric|min:0',
        ];
    }
}