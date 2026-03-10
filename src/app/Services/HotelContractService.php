<?php

namespace App\Services;

use App\Models\HotelContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class HotelContractService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = HotelContract::with(['roomRates', 'createdBy']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('hotel_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('contract_code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('hotel_city', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('destination', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('area', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        if (!empty($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        if (!empty($filters['area'])) {
            $query->where('area', 'like', '%' . $filters['area'] . '%');
        }

        return $query->latest()->paginate(10);
    }

    public function create(array $data, ?string $userId): HotelContract
    {
        return DB::transaction(function () use ($data, $userId) {
            $contract = HotelContract::create([
                ...$data['contract'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if (!empty($data['room_rates'])) {
                foreach ($data['room_rates'] as $rate) {
                    if (!empty($rate['room_type'])) {
                        $contract->roomRates()->create($rate);
                    }
                }
            }

            if (!empty($data['season_surcharges'])) {
                foreach ($data['season_surcharges'] as $surcharge) {
                    if (!empty($surcharge['season_name'])) {
                        $contract->seasonSurcharges()->create($surcharge);
                    }
                }
            }

            if (!empty($data['blackout_dates'])) {
                foreach ($data['blackout_dates'] as $blackout) {
                    if (!empty($blackout['start_date'])) {
                        $contract->blackoutDates()->create($blackout);
                    }
                }
            }

            return $contract;
        });
    }

    public function update(HotelContract $contract, array $data, ?string $userId): HotelContract
    {
        return DB::transaction(function () use ($contract, $data, $userId) {
            $contract->update([
                ...$data['contract'],
                'updated_by' => $userId,
            ]);

            $contract->roomRates()->delete();
            if (!empty($data['room_rates'])) {
                foreach ($data['room_rates'] as $rate) {
                    if (!empty($rate['room_type'])) {
                        $contract->roomRates()->create($rate);
                    }
                }
            }

            $contract->seasonSurcharges()->delete();
            if (!empty($data['season_surcharges'])) {
                foreach ($data['season_surcharges'] as $surcharge) {
                    if (!empty($surcharge['season_name'])) {
                        $contract->seasonSurcharges()->create($surcharge);
                    }
                }
            }

            $contract->blackoutDates()->delete();
            if (!empty($data['blackout_dates'])) {
                foreach ($data['blackout_dates'] as $blackout) {
                    if (!empty($blackout['start_date'])) {
                        $contract->blackoutDates()->create($blackout);
                    }
                }
            }

            $contract->updateStatus();
            return $contract->fresh();
        });
    }

    public function delete(HotelContract $contract): void
    {
        $contract->delete();
    }

    public function updateAllStatuses(): void
    {
        HotelContract::all()->each->updateStatus();
    }
}