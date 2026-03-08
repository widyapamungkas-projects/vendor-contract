<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Contract
            'contract.hotel_name'    => 'required|string|max:255',
            'contract.hotel_address' => 'nullable|string',
            'contract.hotel_city'    => 'nullable|string|max:100',
            'contract.hotel_country' => 'required|string|max:100',
            'contract.pic_name'      => 'required|string|max:255',
            'contract.pic_phone'     => 'nullable|string|max:20',
            'contract.pic_email'     => 'nullable|email',
            'contract.contract_type' => 'required|in:regular,campaign',
            'contract.price_category'=> 'required|in:FIT,GIT',
            'contract.currency'      => 'required|string|max:10',
            'contract.valid_from'    => 'required|date',
            'contract.valid_until'   => 'required|date|after:contract.valid_from',
            'contract.notes'         => 'nullable|string',

            // Room rates
            'room_rates.*.room_type'      => 'nullable|string|max:100',
            'room_rates.*.rate'           => 'nullable|numeric|min:0',
            'room_rates.*.extra_bed_rate' => 'nullable|numeric|min:0',
            'room_rates.*.breakfast_rate' => 'nullable|numeric|min:0',

            // Season surcharges
            'season_surcharges.*.season_name'      => 'nullable|string|max:100',
            'season_surcharges.*.surcharge_amount' => 'nullable|numeric|min:0',
            'season_surcharges.*.surcharge_type'   => 'nullable|in:fixed,percentage',
            'season_surcharges.*.start_date'       => 'nullable|date',
            'season_surcharges.*.end_date'         => 'nullable|date',

            // Blackout dates
            'blackout_dates.*.start_date' => 'nullable|date',
            'blackout_dates.*.end_date'   => 'nullable|date',
            'blackout_dates.*.reason'     => 'nullable|string|max:255',
        ];
    }
}
