<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'contract_code'   => $this->contract_code,
            'hotel_name'      => $this->hotel_name,
            'hotel_address'   => $this->hotel_address,
            'hotel_city'      => $this->hotel_city,
            'hotel_country'   => $this->hotel_country,
            'pic_name'        => $this->pic_name,
            'pic_phone'       => $this->pic_phone,
            'pic_email'       => $this->pic_email,
            'contract_type'   => $this->contract_type,
            'price_category'  => $this->price_category,
            'currency'        => $this->currency,
            'valid_from'      => $this->valid_from->format('Y-m-d'),
            'valid_until'     => $this->valid_until->format('Y-m-d'),
            'status'          => $this->status,
            'notes'           => $this->notes,
            'room_rates'      => $this->whenLoaded('roomRates', function () {
                return $this->roomRates->map(fn($r) => [
                    'id'             => $r->id,
                    'room_type'      => $r->room_type,
                    'rate'           => (float) $r->rate,
                    'extra_bed_rate' => (float) $r->extra_bed_rate,
                    'breakfast_rate' => (float) $r->breakfast_rate,
                ]);
            }),
            'season_surcharges' => $this->whenLoaded('seasonSurcharges', function () {
                return $this->seasonSurcharges->map(fn($s) => [
                    'id'               => $s->id,
                    'season_name'      => $s->season_name,
                    'surcharge_amount' => (float) $s->surcharge_amount,
                    'surcharge_type'   => $s->surcharge_type,
                    'start_date'       => $s->start_date->format('Y-m-d'),
                    'end_date'         => $s->end_date->format('Y-m-d'),
                ]);
            }),
            'blackout_dates' => $this->whenLoaded('blackoutDates', function () {
                return $this->blackoutDates->map(fn($b) => [
                    'id'         => $b->id,
                    'start_date' => $b->start_date->format('Y-m-d'),
                    'end_date'   => $b->end_date->format('Y-m-d'),
                    'reason'     => $b->reason,
                ]);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}