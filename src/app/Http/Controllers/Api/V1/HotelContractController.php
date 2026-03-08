<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelContractRequest;
use App\Http\Resources\HotelContractResource;
use App\Models\HotelContract;
use App\Services\HotelContractService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HotelContractController extends Controller
{
    public function __construct(
        private HotelContractService $service
    ) {}

    // GET /api/v1/hotel-contracts
    public function index(Request $request): JsonResponse
    {
        $contracts = $this->service->getAll($request->only(['search', 'status', 'contract_type']));

        return response()->json([
            'success' => true,
            'data'    => HotelContractResource::collection($contracts),
            'meta'    => [
                'current_page' => $contracts->currentPage(),
                'last_page'    => $contracts->lastPage(),
                'per_page'     => $contracts->perPage(),
                'total'        => $contracts->total(),
            ],
        ]);
    }

    // POST /api/v1/hotel-contracts
    public function store(HotelContractRequest $request): JsonResponse
    {
        $contract = $this->service->create($request->validated(), null);

        return response()->json([
            'success' => true,
            'message' => 'Hotel contract created successfully.',
            'data'    => new HotelContractResource($contract),
        ], 201);
    }

    // GET /api/v1/hotel-contracts/{id}
    public function show(HotelContract $hotelContract): JsonResponse
    {
        $hotelContract->load(['roomRates', 'seasonSurcharges', 'blackoutDates']);

        return response()->json([
            'success' => true,
            'data'    => new HotelContractResource($hotelContract),
        ]);
    }

    // PUT /api/v1/hotel-contracts/{id}
    public function update(HotelContractRequest $request, HotelContract $hotelContract): JsonResponse
    {
        $contract = $this->service->update($hotelContract, $request->validated(), null);

        return response()->json([
            'success' => true,
            'message' => 'Hotel contract updated successfully.',
            'data'    => new HotelContractResource($contract),
        ]);
    }

    // DELETE /api/v1/hotel-contracts/{id}
    public function destroy(HotelContract $hotelContract): JsonResponse
    {
        $this->service->delete($hotelContract);

        return response()->json([
            'success' => true,
            'message' => 'Hotel contract deleted successfully.',
        ]);
    }

    // GET /api/v1/hotel-contracts/stats
    public function stats(): JsonResponse
    {
        $stats = [
            'total'         => HotelContract::count(),
            'active'        => HotelContract::where('status', 'active')->count(),
            'expiring_soon' => HotelContract::where('status', 'expiring_soon')->count(),
            'expired'       => HotelContract::where('status', 'expired')->count(),
        ];

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}