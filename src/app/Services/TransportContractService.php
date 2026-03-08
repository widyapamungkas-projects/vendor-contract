<?php

namespace App\Services;

use App\Models\TransportContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TransportContractService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = TransportContract::with(['vehicles.rates']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('vendor_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('contract_code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('vendor_city', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10);
    }

    public function create(array $data, ?string $userId): TransportContract
    {
        return DB::transaction(function () use ($data, $userId) {
            $contract = TransportContract::create([
                ...$data['contract'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if (!empty($data['vehicles'])) {
                foreach ($data['vehicles'] as $vehicleData) {
                    if (empty($vehicleData['vehicle_name'])) continue;

                    $rates = $vehicleData['rates'] ?? [];
                    unset($vehicleData['rates']);

                    $vehicle = $contract->vehicles()->create($vehicleData);

                    foreach ($rates as $rate) {
                        if (empty($rate['route_name'])) continue;
                        $vehicle->rates()->create($rate);
                    }
                }
            }

            return $contract;
        });
    }

    public function update(TransportContract $contract, array $data, ?string $userId): TransportContract
    {
        return DB::transaction(function () use ($contract, $data, $userId) {
            $contract->update([
                ...$data['contract'],
                'updated_by' => $userId,
            ]);

            // Delete old vehicles (cascade deletes rates)
            $contract->vehicles()->each(function ($vehicle) {
                $vehicle->rates()->delete();
                $vehicle->delete();
            });

            if (!empty($data['vehicles'])) {
                foreach ($data['vehicles'] as $vehicleData) {
                    if (empty($vehicleData['vehicle_name'])) continue;

                    $rates = $vehicleData['rates'] ?? [];
                    unset($vehicleData['rates']);

                    $vehicle = $contract->vehicles()->create($vehicleData);

                    foreach ($rates as $rate) {
                        if (empty($rate['route_name'])) continue;
                        $vehicle->rates()->create($rate);
                    }
                }
            }

            $contract->updateStatus();
            return $contract->fresh();
        });
    }

    public function delete(TransportContract $contract): void
    {
        $contract->delete();
    }
}