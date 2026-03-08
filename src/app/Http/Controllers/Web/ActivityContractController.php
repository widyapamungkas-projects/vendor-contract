<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityContractRequest;
use App\Models\ActivityContract;
use App\Services\ActivityContractService;
use Illuminate\Http\Request;

class ActivityContractController extends Controller
{
    public function __construct(
        private ActivityContractService $service
    ) {}

    public function index(Request $request)
    {
        $contracts = $this->service->getAll($request->only(['search', 'status']));
        return view('activity-contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('activity-contracts.create');
    }

    public function store(ActivityContractRequest $request)
    {
        $contract = $this->service->create($request->validated(), null);
        return redirect()
            ->route('activity-contracts.show', $contract)
            ->with('success', __('contracts.activity_create') . ' berhasil!');
    }

    public function show(ActivityContract $activityContract)
    {
        $activityContract->load(['items.rates']);
        return view('activity-contracts.show', ['contract' => $activityContract]);
    }

    public function edit(ActivityContract $activityContract)
    {
        $activityContract->load(['items.rates']);
        return view('activity-contracts.edit', ['contract' => $activityContract]);
    }

    public function update(ActivityContractRequest $request, ActivityContract $activityContract)
    {
        $this->service->update($activityContract, $request->validated(), null);
        return redirect()
            ->route('activity-contracts.show', $activityContract)
            ->with('success', __('contracts.activity_edit') . ' berhasil!');
    }

    public function destroy(ActivityContract $activityContract)
    {
        $this->service->delete($activityContract);
        return redirect()
            ->route('activity-contracts.index')
            ->with('success', 'Kontrak activity berhasil dihapus.');
    }
}