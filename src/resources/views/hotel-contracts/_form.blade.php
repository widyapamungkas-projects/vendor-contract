{{-- ERROR SUMMARY --}}
@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- SECTION: Basic Info --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
        <i class="fa-solid fa-circle-info mr-2 text-blue-600"></i>Basic Information
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.hotel_name') }} *</label>
            <input type="text" name="contract[hotel_name]"
                   value="{{ old('contract.hotel_name', $contract->hotel_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('contract.hotel_name') border-red-500 @enderror">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.hotel_country') }} *</label>
            <input type="text" name="contract[hotel_country]"
                   value="{{ old('contract.hotel_country', $contract->hotel_country ?? 'Indonesia') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
            <input type="text" name="contract[destination]"
                   value="{{ old('contract.destination', $contract->destination ?? '') }}"
                   placeholder="e.g. Bali, Lombok, Malang"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
            <input type="text" name="contract[area]"
                   value="{{ old('contract.area', $contract->area ?? '') }}"
                   placeholder="e.g. Kuta, Ubud, Seminyak"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.pic_name') }} *</label>
            <input type="text" name="contract[pic_name]"
                   value="{{ old('contract.pic_name', $contract->pic_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('contract.pic_name') border-red-500 @enderror">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.contract_type') }} *</label>
            <select name="contract[contract_type]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="regular"  {{ old('contract.contract_type', $contract->contract_type ?? '') == 'regular'  ? 'selected' : '' }}>{{ __('contracts.regular') }}</option>
                <option value="campaign" {{ old('contract.contract_type', $contract->contract_type ?? '') == 'campaign' ? 'selected' : '' }}>{{ __('contracts.campaign') }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.price_category') }} *</label>
            <select name="contract[price_category]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="FIT" {{ old('contract.price_category', $contract->price_category ?? '') == 'FIT' ? 'selected' : '' }}>FIT</option>
                <option value="GIT" {{ old('contract.price_category', $contract->price_category ?? '') == 'GIT' ? 'selected' : '' }}>GIT</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.currency') }} *</label>
            <select name="contract[currency]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="IDR" {{ old('contract.currency', $contract->currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>IDR</option>
                <option value="USD" {{ old('contract.currency', $contract->currency ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                <option value="SGD" {{ old('contract.currency', $contract->currency ?? '') == 'SGD' ? 'selected' : '' }}>SGD</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.valid_from') }} *</label>
            <input type="date" name="contract[valid_from]"
                   value="{{ old('contract.valid_from', isset($contract->valid_from) ? $contract->valid_from->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('contract.valid_from') border-red-500 @enderror">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.valid_until') }} *</label>
            <input type="date" name="contract[valid_until]"
                   value="{{ old('contract.valid_until', isset($contract->valid_until) ? $contract->valid_until->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('contract.valid_until') border-red-500 @enderror">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('contracts.notes') }}</label>
            <textarea name="contract[notes]" rows="3"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('contract.notes', $contract->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- SECTION: Room Rates --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700">
            <i class="fa-solid fa-bed mr-2 text-blue-600"></i>{{ __('contracts.room_rates') }}
        </h2>
        <button type="button" onclick="addRoom()"
                class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded hover:bg-blue-100">
            <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_room') }}
        </button>
    </div>
    <div id="room-rates-container" class="space-y-3">
        @php $rooms = old('room_rates', isset($contract) ? $contract->roomRates->toArray() : [[]]) @endphp
        @foreach($rooms as $i => $room)
        <div class="room-rate-row grid grid-cols-2 md:grid-cols-5 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.room_type') }}</label>
                <input type="text" name="room_rates[{{ $i }}][room_type]" value="{{ $room['room_type'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.rate') }}</label>
                <input type="number" name="room_rates[{{ $i }}][rate]" value="{{ $room['rate'] ?? '' }}" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.extra_bed') }}</label>
                <input type="number" name="room_rates[{{ $i }}][extra_bed_rate]" value="{{ $room['extra_bed_rate'] ?? '' }}" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Child &amp; Breakfast</label>
                <input type="number" name="room_rates[{{ $i }}][breakfast_rate]" value="{{ $room['breakfast_rate'] ?? '' }}" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><button type="button" onclick="this.closest('.room-rate-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
        </div>
        @endforeach
    </div>
</div>

{{-- SECTION: Season Surcharges --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700">
            <i class="fa-solid fa-sun mr-2 text-yellow-500"></i>{{ __('contracts.season_surcharges') }}
        </h2>
        <button type="button" onclick="addSeason()"
                class="text-sm bg-yellow-50 text-yellow-700 px-3 py-1 rounded hover:bg-yellow-100">
            <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_season') }}
        </button>
    </div>
    <p class="text-xs text-gray-400 mb-3">
        <i class="fa-solid fa-circle-info mr-1"></i>
        Pilih tanggal via date picker — tampilan otomatis format DD-MMM-YYYY
    </p>
    <div id="season-container" class="space-y-3">
        @php
            $seasons = old('season_surcharges', isset($contract) ? $contract->seasonSurcharges->toArray() : [
                ['season_name' => 'High Season', 'surcharge_amount' => '', 'surcharge_type' => 'fixed', 'start_date' => date('Y').'-07-01', 'end_date' => date('Y').'-08-31'],
                ['season_name' => 'Peak Season', 'surcharge_amount' => '', 'surcharge_type' => 'fixed', 'start_date' => date('Y').'-12-20', 'end_date' => (date('Y')+1).'-01-05'],
            ]);
        @endphp
        @foreach($seasons as $i => $season)
        <div class="season-row grid grid-cols-2 md:grid-cols-6 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.season_name') }}</label>
                <input type="text" name="season_surcharges[{{ $i }}][season_name]" value="{{ $season['season_name'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.surcharge_amount') }}</label>
                <input type="number" name="season_surcharges[{{ $i }}][surcharge_amount]" value="{{ $season['surcharge_amount'] ?? '' }}" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="season_surcharges[{{ $i }}][start_date]"
                       value="{{ isset($season['start_date']) ? \Carbon\Carbon::parse($season['start_date'])->format('Y-m-d') : '' }}"
                       class="w-full border rounded px-2 py-1 text-sm season-start"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Until</label>
                <input type="date" name="season_surcharges[{{ $i }}][end_date]"
                       value="{{ isset($season['end_date']) ? \Carbon\Carbon::parse($season['end_date'])->format('Y-m-d') : '' }}"
                       class="w-full border rounded px-2 py-1 text-sm season-end"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Display</label>
                <input type="text" readonly
                       value="{{ isset($season['start_date']) ? \Carbon\Carbon::parse($season['start_date'])->format('d-M-Y').' – '.\Carbon\Carbon::parse($season['end_date'])->format('d-M-Y') : '' }}"
                       class="w-full border rounded px-2 py-1 text-sm bg-gray-100 text-gray-600 season-display"
                       placeholder="01-Jul-2026 – 31-Aug-2026"></div>
            <div><button type="button" onclick="this.closest('.season-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
            <input type="hidden" name="season_surcharges[{{ $i }}][surcharge_type]" value="fixed">
        </div>
        @endforeach
    </div>
</div>

{{-- SECTION: Blackout Dates --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700">
            <i class="fa-solid fa-calendar-xmark mr-2 text-red-500"></i>{{ __('contracts.blackout_dates') }}
        </h2>
        <button type="button" onclick="addBlackout()"
                class="text-sm bg-red-50 text-red-700 px-3 py-1 rounded hover:bg-red-100">
            <i class="fa-solid fa-plus mr-1"></i>{{ __('contracts.add_blackout') }}
        </button>
    </div>
    <div id="blackout-container" class="space-y-3">
        @php $blackouts = old('blackout_dates', isset($contract) ? $contract->blackoutDates->toArray() : []) @endphp
        @foreach($blackouts as $i => $blackout)
        <div class="blackout-row grid grid-cols-1 md:grid-cols-4 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.valid_from') }}</label>
                <input type="date" name="blackout_dates[{{ $i }}][start_date]"
                       value="{{ isset($blackout['start_date']) ? \Carbon\Carbon::parse($blackout['start_date'])->format('Y-m-d') : '' }}"
                       class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.valid_until') }}</label>
                <input type="date" name="blackout_dates[{{ $i }}][end_date]"
                       value="{{ isset($blackout['end_date']) ? \Carbon\Carbon::parse($blackout['end_date'])->format('Y-m-d') : '' }}"
                       class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.reason') }}</label>
                <input type="text" name="blackout_dates[{{ $i }}][reason]" value="{{ $blackout['reason'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><button type="button" onclick="this.closest('.blackout-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
        </div>
        @endforeach
    </div>
</div>

<script>
let roomIndex     = {{ count($rooms ?? [[]]) }};
let blackoutIndex = {{ count($blackouts ?? []) }};
@php $seasonCount = count($seasons ?? []); @endphp
let seasonIndex   = {{ $seasonCount }};

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function fmtDate(val) {
    if (!val) return '';
    const d = new Date(val);
    return String(d.getDate()).padStart(2,'0') + '-' + MONTHS[d.getMonth()] + '-' + d.getFullYear();
}

// Auto-update display field when date picker changes
document.addEventListener('change', function(e) {
    const row = e.target.closest('.season-row');
    if (!row) return;
    const start = row.querySelector('.season-start')?.value;
    const end   = row.querySelector('.season-end')?.value;
    const disp  = row.querySelector('.season-display');
    if (disp) disp.value = (start && end) ? fmtDate(start) + ' – ' + fmtDate(end) : '';
});

function addRoom() {
    const c = document.getElementById('room-rates-container');
    c.insertAdjacentHTML('beforeend', `
        <div class="room-rate-row grid grid-cols-2 md:grid-cols-5 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.room_type') }}</label>
                <input type="text" name="room_rates[${roomIndex}][room_type]" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.rate') }}</label>
                <input type="number" name="room_rates[${roomIndex}][rate]" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.extra_bed') }}</label>
                <input type="number" name="room_rates[${roomIndex}][extra_bed_rate]" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.breakfast') }}</label>
                <input type="number" name="room_rates[${roomIndex}][breakfast_rate]" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><button type="button" onclick="this.closest('.room-rate-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
        </div>`);
    roomIndex++;
}

function addBlackout() {
    const c = document.getElementById('blackout-container');
    c.insertAdjacentHTML('beforeend', `
        <div class="blackout-row grid grid-cols-1 md:grid-cols-4 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.valid_from') }}</label>
                <input type="date" name="blackout_dates[${blackoutIndex}][start_date]" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.valid_until') }}</label>
                <input type="date" name="blackout_dates[${blackoutIndex}][end_date]" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">{{ __('contracts.reason') }}</label>
                <input type="text" name="blackout_dates[${blackoutIndex}][reason]" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><button type="button" onclick="this.closest('.blackout-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
        </div>`);
    blackoutIndex++;
}

function addSeason() {
    const c = document.getElementById('season-container');
    c.insertAdjacentHTML('beforeend', `
        <div class="season-row grid grid-cols-2 md:grid-cols-6 gap-2 items-end border rounded p-3 bg-gray-50">
            <div><label class="block text-xs text-gray-500 mb-1">Season Name</label>
                <input type="text" name="season_surcharges[${seasonIndex}][season_name]" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Amount</label>
                <input type="number" name="season_surcharges[${seasonIndex}][surcharge_amount]" step="0.01" class="w-full border rounded px-2 py-1 text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="season_surcharges[${seasonIndex}][start_date]" class="w-full border rounded px-2 py-1 text-sm season-start"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Until</label>
                <input type="date" name="season_surcharges[${seasonIndex}][end_date]" class="w-full border rounded px-2 py-1 text-sm season-end"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Display</label>
                <input type="text" readonly placeholder="01-Jul-2026 – 31-Aug-2026"
                       class="w-full border rounded px-2 py-1 text-sm bg-gray-100 text-gray-600 season-display"></div>
            <div><button type="button" onclick="this.closest('.season-row').remove()"
                    class="w-full bg-red-50 text-red-600 px-2 py-1 rounded text-sm hover:bg-red-100"><i class="fa-solid fa-trash"></i></button></div>
            <input type="hidden" name="season_surcharges[${seasonIndex}][surcharge_type]" value="fixed">
        </div>`);
    seasonIndex++;
}
</script>
