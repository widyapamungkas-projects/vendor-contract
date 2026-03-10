<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'contract.vendor_name'    => 'required|string|max:255',
            'contract.vendor_city'    => 'nullable|string|max:255',
            'contract.vendor_country' => 'nullable|string|max:255',
            'contract.destination'    => 'nullable|string|max:100',
            'contract.area'           => 'nullable|string|max:100',
            'contract.pic_name'       => 'required|string|max:255',
            'contract.pic_phone'      => 'nullable|string|max:50',
            'contract.pic_email'      => 'nullable|email|max:255',
            'contract.price_category' => 'required|in:FIT,GIT',
            'contract.currency'       => 'required|string|max:10',
            'contract.valid_from'     => 'required|date',
            'contract.valid_until'    => 'required|date|after:contract.valid_from',
            'contract.notes'          => 'nullable|string',

            'items'                        => 'nullable|array',
            'items.*.activity_type'        => 'required|string',
            'items.*.activity_name'        => 'required|string|max:255',
            'items.*.duration'             => 'nullable|string|max:100',
            'items.*.min_pax'              => 'nullable|integer|min:1',
            'items.*.notes'                => 'nullable|string',
            'items.*.rates'                => 'nullable|array',
            'items.*.rates.*.rate_type'    => 'required|in:per_pax,per_group',
            'items.*.rates.*.pax_type'     => 'nullable|in:adult,child,infant',
            'items.*.rates.*.min_pax'      => 'nullable|integer|min:1',
            'items.*.rates.*.price'        => 'required|numeric|min:0',
        ];
    }
}