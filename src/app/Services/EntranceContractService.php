<?php
namespace App\Services;

use App\Models\EntranceContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class EntranceContractService {
    public function getAll(array $filters = []): LengthAwarePaginator {
        $query = EntranceContract::with(['tickets.rates']);
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('vendor_name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('contract_code', 'like', '%'.$filters['search'].'%')
                  ->orWhere('vendor_city', 'like', '%'.$filters['search'].'%');
            });
        }
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        return $query->latest()->paginate(10);
    }

    public function create(array $data, ?string $userId): EntranceContract {
        return DB::transaction(function () use ($data, $userId) {
            $contract = EntranceContract::create([...$data['contract'], 'created_by' => $userId, 'updated_by' => $userId]);
            foreach ($data['tickets'] ?? [] as $ticketData) {
                if (empty($ticketData['attraction_name'])) continue;
                $rates = $ticketData['rates'] ?? [];
                unset($ticketData['rates']);
                $ticket = $contract->tickets()->create($ticketData);
                foreach ($rates as $rate) {
                    if (!isset($rate['price']) || $rate['price'] === '') continue;
                    $ticket->rates()->create($rate);
                }
            }
            return $contract;
        });
    }

    public function update(EntranceContract $contract, array $data, ?string $userId): EntranceContract {
        return DB::transaction(function () use ($contract, $data, $userId) {
            $contract->update([...$data['contract'], 'updated_by' => $userId]);
            $contract->tickets()->each(function ($t) { $t->rates()->delete(); $t->delete(); });
            foreach ($data['tickets'] ?? [] as $ticketData) {
                if (empty($ticketData['attraction_name'])) continue;
                $rates = $ticketData['rates'] ?? [];
                unset($ticketData['rates']);
                $ticket = $contract->tickets()->create($ticketData);
                foreach ($rates as $rate) {
                    if (!isset($rate['price']) || $rate['price'] === '') continue;
                    $ticket->rates()->create($rate);
                }
            }
            $contract->updateStatus();
            return $contract->fresh();
        });
    }

    public function delete(EntranceContract $contract): void { $contract->delete(); }
}
