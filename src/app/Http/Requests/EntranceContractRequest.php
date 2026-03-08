<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntranceContractRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'contract.vendor_name'    => 'required|string|max:255',
            'contract.vendor_city'    => 'required|string|max:255',
            'contract.vendor_country' => 'required|string|max:255',
            'contract.pic_name'       => 'required|string|max:255',
            'contract.pic_phone'      => 'nullable|string|max:50',
            'contract.pic_email'      => 'nullable|email|max:255',
            'contract.price_category' => 'required|in:FIT,GIT',
            'contract.currency'       => 'required|string|max:10',
            'contract.valid_from'     => 'required|date',
            'contract.valid_until'    => 'required|date|after:contract.valid_from',
            'contract.notes'          => 'nullable|string',
            'tickets'                          => 'nullable|array',
            'tickets.*.attraction_type'        => 'required|string',
            'tickets.*.attraction_name'        => 'required|string|max:255',
            'tickets.*.notes'                  => 'nullable|string',
            'tickets.*.rates'                  => 'nullable|array',
            'tickets.*.rates.*.pax_type'       => 'required|in:adult,child,infant',
            'tickets.*.rates.*.price'          => 'required|numeric|min:0',
        ];
    }
}
