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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Name *</label>
            <input type="text" name="contract[vendor_name]" value="{{ old('contract.vendor_name', $contract->vendor_name ?? '') }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        
        
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
            <input type="text" name="contract[destination]"
                   value="{{ old('contract.destination', $contract->destination ?? '') }}"
                   placeholder="e.g. Bali, Lombok, Malang"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.pic_name') }} *</label>
            <input type="text" name="contract[pic_name]"
                   value="{{ old('contract.pic_name', $contract->pic_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.pic_phone') }}</label>
            <input type="text" name="contract[pic_phone]"
                   value="{{ old('contract.pic_phone', $contract->pic_phone ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.pic_email') }}</label>
            <input type="email" name="contract[pic_email]"
                   value="{{ old('contract.pic_email', $contract->pic_email ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
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

{{-- Activity Items --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700">
            <i class="fa-solid fa-person-hiking mr-2 text-blue-600"></i>{{ __('contracts.activity_items') }}
        </h2>
        <button type="button" onclick="addActivity()"
                class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded hover:bg-blue-100">
            <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_activity') }}
        </button>
    </div>

    @php
        $items = old('items', isset($contract) ? $contract->items->map(function($item) {
            $arr = $item->toArray();
            $arr['rates'] = $item->rates->toArray();
            return $arr;
        })->toArray() : [[]]);
        $activityTypes = ['rafting','cycling','trekking','water_sports','cultural_tour','cooking_class','atv_offroad','spa_wellness','other'];
    @endphp

    <div id="items-container" class="space-y-4">
        @foreach($items as $ii => $item)
        <div class="item-block border-2 border-green-100 rounded-lg p-4 bg-green-50/30">
            {{-- Item Header --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.activity_type') }} *</label>
                    <select name="items[{{ $ii }}][activity_type]" class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                        @foreach($activityTypes as $type)
                        <option value="{{ $type }}" {{ ($item['activity_type'] ?? 'other') == $type ? 'selected' : '' }}>
                            {{ __('contracts.' . $type) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.activity_name') }} *</label>
                    <input type="text" name="items[{{ $ii }}][activity_name]"
                           value="{{ $item['activity_name'] ?? '' }}"
                           placeholder="e.g. Telaga Waja Rafting"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.duration') }}</label>
                    <input type="text" name="items[{{ $ii }}][duration]"
                           value="{{ $item['duration'] ?? '' }}"
                           placeholder="e.g. 2 jam / Half Day"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('contracts.min_pax') }}</label>
                    <input type="number" name="items[{{ $ii }}][min_pax]"
                           value="{{ $item['min_pax'] ?? 1 }}" min="1"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Notes</label>
                <input type="text" name="items[{{ $ii }}][notes]"
                       value="{{ $item['notes'] ?? '' }}"
                       placeholder="Include: equipment, transport, meal, etc."
                       class="w-full border rounded px-2 py-1.5 text-sm bg-white">
            </div>

            {{-- Rates --}}
            <div class="bg-white rounded border p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        <i class="fa-solid fa-tag mr-1"></i>{{ __('contracts.price') }}
                    </span>
                    <button type="button" onclick="addRate(this, {{ $ii }})"
                            class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded hover:bg-green-100">
                        <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_rate') }}
                    </button>
                </div>
                <div class="rates-container space-y-2">
                    @php $rates = $item['rates'] ?? [['rate_type' => 'per_pax', 'pax_type' => 'adult']]; @endphp
                    @foreach($rates as $ri => $rate)
                    <div class="rate-row grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.rate_type') }}</label>
                            <select name="items[{{ $ii }}][rates][{{ $ri }}][rate_type]"
                                    class="w-full border rounded px-2 py-1 text-xs"
                                    onchange="togglePaxType(this)">
                                <option value="per_pax"   {{ ($rate['rate_type'] ?? 'per_pax') == 'per_pax'   ? 'selected' : '' }}>{{ __('contracts.per_pax') }}</option>
                                <option value="per_group" {{ ($rate['rate_type'] ?? '') == 'per_group' ? 'selected' : '' }}>{{ __('contracts.per_group') }}</option>
                            </select>
                        </div>
                        <div class="pax-type-field" style="{{ ($rate['rate_type'] ?? 'per_pax') == 'per_group' ? 'display:none' : '' }}">
                            <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.pax_type') }}</label>
                            <select name="items[{{ $ii }}][rates][{{ $ri }}][pax_type]"
                                    class="w-full border rounded px-2 py-1 text-xs">
                                <option value="adult"  {{ ($rate['pax_type'] ?? 'adult') == 'adult'  ? 'selected' : '' }}>{{ __('contracts.adult') }}</option>
                                <option value="child"  {{ ($rate['pax_type'] ?? '') == 'child'  ? 'selected' : '' }}>{{ __('contracts.child') }}</option>
                                <option value="infant" {{ ($rate['pax_type'] ?? '') == 'infant' ? 'selected' : '' }}>{{ __('contracts.infant') }}</option>
                            </select>
                        </div>
                        <div class="min-pax-field" style="{{ ($rate['rate_type'] ?? 'per_pax') != 'per_group' ? 'display:none' : '' }}">
                            <label class="block text-xs text-gray-400 mb-1">Min Pax</label>
                            <input type="number" name="items[{{ $ii }}][rates][{{ $ri }}][min_pax]"
                                   value="{{ $rate['min_pax'] ?? '' }}" min="1"
                                   class="w-full border rounded px-2 py-1 text-xs">
                        </div>
                        <div class="flex gap-1">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-400 mb-1">{{ __('contracts.price') }}</label>
                                <input type="number" name="items[{{ $ii }}][rates][{{ $ri }}][price]"
                                       value="{{ $rate['price'] ?? '' }}" step="1000"
                                       class="w-full border rounded px-2 py-1 text-xs">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="this.closest('.rate-row').remove()"
                                        class="bg-red-50 text-red-500 px-2 py-1 rounded text-xs hover:bg-red-100">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-2 flex justify-end">
                <button type="button" onclick="this.closest('.item-block').remove()"
                        class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100">
                    <i class="fa-solid fa-trash mr-1"></i>Remove Activity
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
let itemIndex = {{ count($items) }};
const rateIndexes = {};

const activityTypes = [
    {value:'rafting',       label:'Rafting'},
    {value:'cycling',       label:'Cycling'},
    {value:'trekking',      label:'Trekking'},
    {value:'water_sports',  label:'Water Sports'},
    {value:'cultural_tour', label:'Cultural Tour'},
    {value:'cooking_class', label:'Cooking Class'},
    {value:'atv_offroad',   label:'ATV/Offroad'},
    {value:'spa_wellness',  label:'Spa & Wellness'},
    {value:'other',         label:'Other'},
];

function addActivity() {
    const ii = itemIndex++;
    rateIndexes[ii] = 0;
    const typeOptions = activityTypes.map(t => `<option value="${t.value}">${t.label}</option>`).join('');
    document.getElementById('items-container').insertAdjacentHTML('beforeend', `
        <div class="item-block border-2 border-green-100 rounded-lg p-4 bg-green-50/30">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Type *</label>
                    <select name="items[${ii}][activity_type]" class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                        ${typeOptions}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Activity Name *</label>
                    <input type="text" name="items[${ii}][activity_name]" placeholder="e.g. Telaga Waja Rafting"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Duration</label>
                    <input type="text" name="items[${ii}][duration]" placeholder="e.g. 2 jam"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Min Pax</label>
                    <input type="number" name="items[${ii}][min_pax]" value="1" min="1"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Notes</label>
                <input type="text" name="items[${ii}][notes]" placeholder="Include: equipment, transport, meal, etc."
                       class="w-full border rounded px-2 py-1.5 text-sm bg-white">
            </div>
            <div class="bg-white rounded border p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        <i class="fa-solid fa-tag mr-1"></i>Rates
                    </span>
                    <button type="button" onclick="addRate(this, ${ii})"
                            class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded hover:bg-green-100">
                        <i class="fa-solid fa-plus mr-1"></i>Add Rate
                    </button>
                </div>
                <div class="rates-container space-y-2"></div>
            </div>
            <div class="mt-2 flex justify-end">
                <button type="button" onclick="this.closest('.item-block').remove()"
                        class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100">
                    <i class="fa-solid fa-trash mr-1"></i>Remove Activity
                </button>
            </div>
        </div>`);
}

function addRate(btn, ii) {
    if (!rateIndexes[ii]) rateIndexes[ii] = 0;
    const ri = rateIndexes[ii]++;
    const container = btn.closest('.bg-white.rounded.border').querySelector('.rates-container');
    container.insertAdjacentHTML('beforeend', `
        <div class="rate-row grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Rate Type</label>
                <select name="items[${ii}][rates][${ri}][rate_type]" class="w-full border rounded px-2 py-1 text-xs"
                        onchange="togglePaxType(this)">
                    <option value="per_pax">Per Pax</option>
                    <option value="per_group">Per Group</option>
                </select>
            </div>
            <div class="pax-type-field">
                <label class="block text-xs text-gray-400 mb-1">Pax Type</label>
                <select name="items[${ii}][rates][${ri}][pax_type]" class="w-full border rounded px-2 py-1 text-xs">
                    <option value="adult">Adult</option>
                    <option value="child">Child</option>
                    <option value="infant">Infant</option>
                </select>
            </div>
            <div class="min-pax-field" style="display:none">
                <label class="block text-xs text-gray-400 mb-1">Min Pax</label>
                <input type="number" name="items[${ii}][rates][${ri}][min_pax]" min="1"
                       class="w-full border rounded px-2 py-1 text-xs">
            </div>
            <div class="flex gap-1">
                <div class="flex-1">
                    <label class="block text-xs text-gray-400 mb-1">Price</label>
                    <input type="number" name="items[${ii}][rates][${ri}][price]" step="1000"
                           class="w-full border rounded px-2 py-1 text-xs">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.rate-row').remove()"
                            class="bg-red-50 text-red-500 px-2 py-1 rounded text-xs hover:bg-red-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>`);
}

function togglePaxType(select) {
    const row = select.closest('.rate-row');
    const paxField = row.querySelector('.pax-type-field');
    const minPaxField = row.querySelector('.min-pax-field');
    if (select.value === 'per_group') {
        paxField.style.display = 'none';
        minPaxField.style.display = '';
    } else {
        paxField.style.display = '';
        minPaxField.style.display = 'none';
    }
}
</script>