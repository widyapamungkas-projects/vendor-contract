<?php
namespace App\Services;
use App\Models\ActivityContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityContractService {
    public function getAll(array $filters = []): LengthAwarePaginator {
        $query = ActivityContract::with(['items.rates']);
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('vendor_name','like','%'.$filters['search'].'%')
                  ->orWhere('contract_code','like','%'.$filters['search'].'%')
                  ->orWhere('vendor_city','like','%'.$filters['search'].'%')
                  ->orWhere('destination','like','%'.$filters['search'].'%')
                  ->orWhere('area','like','%'.$filters['search'].'%');
            });
        }
        if (!empty($filters['status'])) $query->where('status',$filters['status']);
        if (!empty($filters['destination'])) $query->where('destination','like','%'.$filters['destination'].'%');
        if (!empty($filters['area'])) $query->where('area','like','%'.$filters['area'].'%');
        return $query->latest()->paginate(10);
    }
    public function create(array $data, ?string $userId): ActivityContract {
        return DB::transaction(function () use ($data,$userId) {
            $contract = ActivityContract::create([...$data['contract'],'created_by'=>$userId,'updated_by'=>$userId]);
            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    if (empty($itemData['activity_name'])) continue;
                    $rates = $itemData['rates'] ?? []; unset($itemData['rates']);
                    $item = $contract->items()->create($itemData);
                    foreach ($rates as $rate) { if (empty($rate['price'])) continue; $item->rates()->create($rate); }
                }
            }
            return $contract;
        });
    }
    public function update(ActivityContract $contract, array $data, ?string $userId): ActivityContract {
        return DB::transaction(function () use ($contract,$data,$userId) {
            $contract->update([...$data['contract'],'updated_by'=>$userId]);
            $contract->items()->each(function ($item) { $item->rates()->delete(); $item->delete(); });
            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    if (empty($itemData['activity_name'])) continue;
                    $rates = $itemData['rates'] ?? []; unset($itemData['rates']);
                    $item = $contract->items()->create($itemData);
                    foreach ($rates as $rate) { if (empty($rate['price'])) continue; $item->rates()->create($rate); }
                }
            }
            $contract->updateStatus(); return $contract->fresh();
        });
    }
    public function delete(ActivityContract $contract): void { $contract->delete(); }
}
