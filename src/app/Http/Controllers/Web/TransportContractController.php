<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransportContractRequest;
use App\Models\TransportContract;
use App\Services\TransportContractService;
use Illuminate\Http\Request;

class TransportContractController extends Controller
{
    public function __construct(
        private TransportContractService $service
    ) {}

    public function index(Request $request)
    {
        $contracts = $this->service->getAll($request->only(['search', 'status']));
        return view('transport-contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('transport-contracts.create');
    }

    public function store(TransportContractRequest $request)
    {
        $contract = $this->service->create($request->validated(), null);
        return redirect()
            ->route('transport-contracts.show', $contract)
            ->with('success', __('contracts.transport_create') . ' berhasil!');
    }

    public function show(TransportContract $transportContract)
    {
        $transportContract->load(['vehicles.rates']);
        return view('transport-contracts.show', ['contract' => $transportContract]);
    }

    public function edit(TransportContract $transportContract)
    {
        $transportContract->load(['vehicles.rates']);
        return view('transport-contracts.edit', ['contract' => $transportContract]);
    }

    public function update(TransportContractRequest $request, TransportContract $transportContract)
    {
        $this->service->update($transportContract, $request->validated(), null);
        return redirect()
            ->route('transport-contracts.show', $transportContract)
            ->with('success', __('contracts.transport_edit') . ' berhasil!');
    }

    public function destroy(TransportContract $transportContract)
    {
        $this->service->delete($transportContract);
        return redirect()
            ->route('transport-contracts.index')
            ->with('success', 'Kontrak transport berhasil dihapus.');
    }
}