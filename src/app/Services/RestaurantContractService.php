<?php
namespace App\Services;

use App\Models\RestaurantContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RestaurantContractService {
    public function getAll(array $filters = []): LengthAwarePaginator {
        $query = RestaurantContract::with(['menus']);
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

    public function create(array $data, ?string $userId): RestaurantContract {
        return DB::transaction(function () use ($data, $userId) {
            $contract = RestaurantContract::create([
                ...$data['contract'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
            foreach ($data['menus'] ?? [] as $menuData) {
                if (empty($menuData['menu_name'])) continue;
                $menuData['child_price'] = round($menuData['adult_price'] * 0.65);
                $contract->menus()->create($menuData);
            }
            return $contract;
        });
    }

    public function update(RestaurantContract $contract, array $data, ?string $userId): RestaurantContract {
        return DB::transaction(function () use ($contract, $data, $userId) {
            $contract->update([...$data['contract'], 'updated_by' => $userId]);
            $contract->menus()->delete();
            foreach ($data['menus'] ?? [] as $menuData) {
                if (empty($menuData['menu_name'])) continue;
                $menuData['child_price'] = round($menuData['adult_price'] * 0.65);
                $contract->menus()->create($menuData);
            }
            $contract->updateStatus();
            return $contract->fresh();
        });
    }

    public function delete(RestaurantContract $contract): void { $contract->delete(); }
}
