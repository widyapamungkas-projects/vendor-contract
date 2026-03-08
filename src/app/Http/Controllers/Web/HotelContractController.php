<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelContractRequest;
use App\Models\HotelContract;
use App\Services\HotelContractService;
use Illuminate\Http\Request;

class HotelContractController extends Controller
{
    public function __construct(
        private HotelContractService $service
    ) {}

    public function index(Request $request)
    {
        $contracts = $this->service->getAll($request->only(['search', 'status', 'contract_type']));
        return view('hotel-contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('hotel-contracts.create');
    }

    public function store(HotelContractRequest $request)
    {
        $contract = $this->service->create($request->validated(), null);
        return redirect()
            ->route('hotel-contracts.show', $contract)
            ->with('success', __('contracts.create') . ' berhasil!');
    }

    public function show(HotelContract $hotelContract)
    {
        $hotelContract->load(['roomRates', 'seasonSurcharges', 'blackoutDates', 'createdBy']);
        return view('hotel-contracts.show', ['contract' => $hotelContract]);
    }

    public function edit(HotelContract $hotelContract)
    {
        $hotelContract->load(['roomRates', 'seasonSurcharges', 'blackoutDates']);
        return view('hotel-contracts.edit', ['contract' => $hotelContract]);
    }

    public function update(HotelContractRequest $request, HotelContract $hotelContract)
    {
        $this->service->update($hotelContract, $request->validated(), null);
        return redirect()
            ->route('hotel-contracts.show', $hotelContract)
            ->with('success', __('contracts.edit') . ' berhasil!');
    }

    public function destroy(HotelContract $hotelContract)
    {
        $this->service->delete($hotelContract);
        return redirect()
            ->route('hotel-contracts.index')
            ->with('success', 'Kontrak berhasil dihapus.');
    }
}