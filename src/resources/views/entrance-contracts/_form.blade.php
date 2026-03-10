@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
    <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="bg-white rounded shadow p-6">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
        <i class="fa-solid fa-ticket mr-2 text-blue-600"></i>Ticket Information
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attraction Type *</label>
            <select name="attraction_type" class="w-full border rounded px-3 py-2 text-sm">
                @foreach(['temple'=>'Temple','museum'=>'Museum','theme_park'=>'Theme Park','natural_attraction'=>'Natural Attraction','cultural_show'=>'Cultural Show','zoo_safari'=>'Zoo/Safari','other'=>'Other'] as $val => $label)
                <option value="{{ $val }}" {{ old('attraction_type', $ticket->attraction_type ?? 'other')==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
            <input type="text" name="destination"
                   value="{{ old('destination', $ticket->destination ?? '') }}"
                   placeholder="e.g. Bali, Lombok"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attraction Name *</label>
            <input type="text" name="attraction_name"
                   value="{{ old('attraction_name', $ticket->attraction_name ?? '') }}"
                   placeholder="e.g. Tanah Lot Temple"
                   class="w-full border rounded px-3 py-2 text-sm @error('attraction_name') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
            <select name="currency" class="w-full border rounded px-3 py-2 text-sm">
                <option value="IDR" {{ old('currency', $ticket->currency ?? 'IDR')=='IDR'?'selected':'' }}>IDR</option>
                <option value="USD" {{ old('currency', $ticket->currency ?? '')=='USD'?'selected':'' }}>USD</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <input type="text" name="notes"
                   value="{{ old('notes', $ticket->notes ?? '') }}"
                   placeholder="e.g. Include: sarong, guide"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        {{-- Prices --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-3">Harga Tiket *</label>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-blue-700 mb-2">
                        <i class="fa-solid fa-person mr-1"></i>Adult
                    </label>
                    <input type="number" name="adult_price"
                           value="{{ old('adult_price', $ticket->adult_price ?? '') }}"
                           step="1000" placeholder="0"
                           class="w-full border rounded px-3 py-2 text-sm @error('adult_price') border-red-500 @enderror">
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-green-700 mb-2">
                        <i class="fa-solid fa-child mr-1"></i>Child
                    </label>
                    <input type="number" name="child_price"
                           value="{{ old('child_price', $ticket->child_price ?? '') }}"
                           step="1000" placeholder="0"
                           class="w-full border rounded px-3 py-2 text-sm @error('child_price') border-red-500 @enderror">
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-yellow-700 mb-2">
                        <i class="fa-solid fa-baby mr-1"></i>Infant
                    </label>
                    <input type="number" name="infant_price"
                           value="{{ old('infant_price', $ticket->infant_price ?? 0) }}"
                           step="1000" placeholder="0"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>
        </div>
    </div>
</div>
