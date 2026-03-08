<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantContractRequest;
use App\Models\RestaurantContract;
use App\Services\RestaurantContractService;
use Illuminate\Http\Request;

class RestaurantContractController extends Controller {
    public function __construct(private RestaurantContractService $service) {}

    public function index(Request $request) {
        $contracts = $this->service->getAll($request->only(['search', 'status']));
        return view('restaurant-contracts.index', compact('contracts'));
    }
    public function create() { return view('restaurant-contracts.create'); }
    public function store(RestaurantContractRequest $request) {
        $contract = $this->service->create($request->validated(), null);
        return redirect()->route('restaurant-contracts.show', $contract)
            ->with('success', 'Restaurant contract berhasil dibuat!');
    }
    public function show(RestaurantContract $restaurantContract) {
        $restaurantContract->load(['menus']);
        return view('restaurant-contracts.show', ['contract' => $restaurantContract]);
    }
    public function edit(RestaurantContract $restaurantContract) {
        $restaurantContract->load(['menus']);
        return view('restaurant-contracts.edit', ['contract' => $restaurantContract]);
    }
    public function update(RestaurantContractRequest $request, RestaurantContract $restaurantContract) {
        $this->service->update($restaurantContract, $request->validated(), null);
        return redirect()->route('restaurant-contracts.show', $restaurantContract)
            ->with('success', 'Restaurant contract berhasil diupdate!');
    }
    public function destroy(RestaurantContract $restaurantContract) {
        $this->service->delete($restaurantContract);
        return redirect()->route('restaurant-contracts.index')
            ->with('success', 'Contract berhasil dihapus.');
    }
}
