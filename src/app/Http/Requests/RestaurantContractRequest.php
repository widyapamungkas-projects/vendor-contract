<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantContractRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
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
            'menus'                          => 'nullable|array',
            'menus.*.menu_name'              => 'required|string|max:255',
            'menus.*.serving_style'          => 'required|in:set_menu,family_set,buffet',
            'menus.*.adult_price'            => 'required|numeric|min:0',
            'menus.*.min_pax'                => 'nullable|integer|min:1',
            'menus.*.menu_details'           => 'nullable|string',
            'menus.*.notes'                  => 'nullable|string',
        ];
    }
}
