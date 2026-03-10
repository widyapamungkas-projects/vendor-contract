@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Basic Info --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
        <i class="fa-solid fa-circle-info mr-2 text-blue-600"></i>Basic Information
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.vendor_name') }} *</label>
            <input type="text" name="contract[vendor_name]"
                   value="{{ old('contract.vendor_name', $contract->vendor_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('contract.vendor_name') border-red-500 @enderror">
        </div>





        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.price_category') }} *</label>
            <select name="contract[price_category]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="FIT" {{ old('contract.price_category', $contract->price_category ?? 'FIT') == 'FIT' ? 'selected' : '' }}>FIT</option>
                <option value="GIT" {{ old('contract.price_category', $contract->price_category ?? '') == 'GIT' ? 'selected' : '' }}>GIT</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.currency') }} *</label>
            <select name="contract[currency]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="IDR" {{ old('contract.currency', $contract->currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>IDR</option>
                <option value="USD" {{ old('contract.currency', $contract->currency ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.valid_from') }} *</label>
            <input type="date" name="contract[valid_from]"
                   value="{{ old('contract.valid_from', isset($contract->valid_from) ? $contract->valid_from->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.valid_until') }} *</label>
            <input type="date" name="contract[valid_until]"
                   value="{{ old('contract.valid_until', isset($contract->valid_until) ? $contract->valid_until->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.notes') }}</label>
            <textarea name="contract[notes]" rows="2"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('contract.notes', $contract->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Vehicles & Rates --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700">
            <i class="fa-solid fa-bus mr-2 text-blue-600"></i>{{ __('contracts.vehicles') }} & {{ __('contracts.routes') }}
        </h2>
        <button type="button" onclick="addVehicle()"
                class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded hover:bg-blue-100">
            <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_vehicle') }}
        </button>
    </div>

    <div id="vehicles-container" class="space-y-4">
        @php
            $vehicles = old('vehicles', isset($contract) ? $contract->vehicles->map(function($v) {
                $arr = $v->toArray();
                $arr['rates'] = $v->rates->toArray();
                return $arr;
            })->toArray() : [[]]);
        @endphp

        @foreach($vehicles as $vi => $vehicle)
        <div class="vehicle-block border-2 border-blue-100 rounded-lg p-4 bg-blue-50/30">
            {{-- Vehicle Header --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.vehicle_category') }} *</label>
                    <select name="vehicles[{{ $vi }}][category]" class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                        <option value="car"  {{ ($vehicle['category'] ?? 'car') == 'car'  ? 'selected' : '' }}>{{ __('contracts.car') }}</option>
                        <option value="van"  {{ ($vehicle['category'] ?? '') == 'van'  ? 'selected' : '' }}>{{ __('contracts.van') }}</option>
                        <option value="bus"  {{ ($vehicle['category'] ?? '') == 'bus'  ? 'selected' : '' }}>{{ __('contracts.bus') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.vehicle_name') }} *</label>
                    <input type="text" name="vehicles[{{ $vi }}][vehicle_name]"
                           value="{{ $vehicle['vehicle_name'] ?? '' }}"
                           placeholder="e.g. Toyota Avanza"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.capacity') }}</label>
                    <input type="number" name="vehicles[{{ $vi }}][capacity]"
                           value="{{ $vehicle['capacity'] ?? '' }}"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.brand') }}</label>
                    <input type="text" name="vehicles[{{ $vi }}][brand]"
                           value="{{ $vehicle['brand'] ?? '' }}"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.vehicle-block').remove()"
                            class="w-full bg-red-50 text-red-600 px-2 py-1.5 rounded text-sm hover:bg-red-100">
                        <i class="fa-solid fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>

            {{-- Routes --}}
            <div class="bg-white rounded border p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        <i class="fa-solid fa-route mr-1"></i>{{ __('contracts.routes') }}
                    </span>
                    <button type="button" onclick="addRoute(this, {{ $vi }})"
                            class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded hover:bg-green-100">
                        <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_route') }}
                    </button>
                </div>
                <div class="routes-container space-y-2">
                    @php $rates = $vehicle['rates'] ?? [[]]; @endphp
                    @foreach($rates as $ri => $rate)
                    <div class="route-row grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.route_name') }}</label>
                            <input type="text" name="vehicles[{{ $vi }}][rates][{{ $ri }}][route_name]"
                                   value="{{ $rate['route_name'] ?? '' }}"
                                   placeholder="e.g. Transfer Tuban/Kuta"
                                   class="w-full border rounded px-2 py-1 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.route_type') }}</label>
                            <select name="vehicles[{{ $vi }}][rates][{{ $ri }}][route_type]"
                                    class="w-full border rounded px-2 py-1 text-xs">
                                @foreach(['airport_transfer','half_day','full_day','half_day_dinner','full_day_dinner','overnight','extra_hour'] as $type)
                                <option value="{{ $type }}" {{ ($rate['route_type'] ?? 'airport_transfer') == $type ? 'selected' : '' }}>
                                    {{ __('contracts.' . $type) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.duration') }}</label>
                            <input type="text" name="vehicles[{{ $vi }}][rates][{{ $ri }}][duration]"
                                   value="{{ $rate['duration'] ?? '' }}"
                                   placeholder="e.g. One Way / 6 jam"
                                   class="w-full border rounded px-2 py-1 text-xs">
                        </div>
                        <div class="flex gap-1">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.price') }}</label>
                                <input type="number" name="vehicles[{{ $vi }}][rates][{{ $ri }}][price]"
                                       value="{{ $rate['price'] ?? '' }}" step="1000"
                                       class="w-full border rounded px-2 py-1 text-xs">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="this.closest('.route-row').remove()"
                                        class="bg-red-50 text-red-500 px-2 py-1 rounded text-xs hover:bg-red-100">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
let vehicleIndex = {{ count($vehicles) }};

function addVehicle() {
    const vi = vehicleIndex++;
    const container = document.getElementById('vehicles-container');
    container.insertAdjacentHTML('beforeend', `
        <div class="vehicle-block border-2 border-blue-100 rounded-lg p-4 bg-blue-50/30">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Category *</label>
                    <select name="vehicles[${vi}][category]" class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                        <option value="car">Car</option>
                        <option value="van">Van</option>
                        <option value="bus">Bus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Vehicle Name *</label>
                    <input type="text" name="vehicles[${vi}][vehicle_name]" placeholder="e.g. Toyota Avanza"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Capacity</label>
                    <input type="number" name="vehicles[${vi}][capacity]"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Brand</label>
                    <input type="text" name="vehicles[${vi}][brand]"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.vehicle-block').remove()"
                            class="w-full bg-red-50 text-red-600 px-2 py-1.5 rounded text-sm hover:bg-red-100">
                        <i class="fa-solid fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>
            <div class="bg-white rounded border p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        <i class="fa-solid fa-route mr-1"></i>Routes & Rates
                    </span>
                    <button type="button" onclick="addRoute(this, ${vi})"
                            class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded hover:bg-green-100">
                        <i class="fa-solid fa-plus mr-1"></i>Add Route
                    </button>
                </div>
                <div class="routes-container space-y-2"></div>
            </div>
        </div>`);
}

const routeIndexes = {};
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".vehicle-block").forEach(function(block, vi) {
        routeIndexes[vi] = block.querySelectorAll(".route-row").length;
    });
});
function addRoute(btn, vi) {
    if (routeIndexes[vi] === undefined) routeIndexes[vi] = 0;
    const ri = routeIndexes[vi]++;
    const container = btn.closest('.bg-white.rounded.border').querySelector('.routes-container');
    container.insertAdjacentHTML('beforeend', `
        <div class="route-row grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Route / Program</label>
                <input type="text" name="vehicles[${vi}][rates][${ri}][route_name]"
                       placeholder="e.g. Transfer Tuban/Kuta"
                       class="w-full border rounded px-2 py-1 text-xs">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Type</label>
                <select name="vehicles[${vi}][rates][${ri}][route_type]" class="w-full border rounded px-2 py-1 text-xs">
                    <option value="airport_transfer">Airport Transfer</option>
                    <option value="half_day">Half Day</option>
                    <option value="full_day">Full Day</option>
                    <option value="half_day_dinner">Half Day + Dinner</option>
                    <option value="full_day_dinner">Full Day + Dinner</option>
                    <option value="overnight">Overnight</option>
                    <option value="extra_hour">Extra Hour</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Duration</label>
                <input type="text" name="vehicles[${vi}][rates][${ri}][duration]"
                       placeholder="e.g. One Way / 6 jam"
                       class="w-full border rounded px-2 py-1 text-xs">
            </div>
            <div class="flex gap-1">
                <div class="flex-1">
                    <label class="block text-xs text-gray-400 mb-1">Price</label>
                    <input type="number" name="vehicles[${vi}][rates][${ri}][price]" step="1000"
                           class="w-full border rounded px-2 py-1 text-xs">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.route-row').remove()"
                            class="bg-red-50 text-red-500 px-2 py-1 rounded text-xs hover:bg-red-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>`);
}
</script>