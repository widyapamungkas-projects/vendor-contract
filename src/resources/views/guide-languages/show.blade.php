@extends('layouts.app')
@section('title', $guideLanguage->language_name . ' — Guide Fee')
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6">
        <div>
            <a href="{{ route('guide-languages.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Guide Fee</a>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $guideLanguage->language_name }} Guide</h2>
            <p class="text-sm text-gray-500">
                {{ $guideLanguage->currency }}
                @if($guideLanguage->destination) · <span class="text-indigo-600 font-semibold">{{ $guideLanguage->destination }}</span>@endif
                @if($guideLanguage->notes) · {{ $guideLanguage->notes }}@endif
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('editInfoModal').classList.remove('hidden')"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold">
                <i class="fa-solid fa-pen-to-square"></i> Edit Info
            </button>
            <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold">
                <i class="fa-solid fa-plus"></i> Add Service
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @php
        $typeColors = [
            'airport_transfer' => 'bg-blue-100 text-blue-700',
            'full_day'         => 'bg-green-100 text-green-700',
            'half_day'         => 'bg-yellow-100 text-yellow-700',
            'overtime'         => 'bg-purple-100 text-purple-700',
            'tipping'          => 'bg-red-100 text-red-700',
            'package'          => 'bg-gray-100 text-gray-600',
        ];
        $serviceTypes = ['airport_transfer','full_day','half_day','overtime','tipping','package'];
    @endphp

    @forelse($guideLanguage->services as $svc)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 overflow-hidden">
        {{-- Service header --}}
        <div class="flex justify-between items-center px-5 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold px-2 py-1 rounded {{ $typeColors[$svc->service_type] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ \App\Models\GuideService::serviceTypeLabel($svc->service_type) }}
                </span>
                <span class="font-semibold text-gray-800">{{ $svc->service_name }}</span>
                <span class="text-gray-400 text-sm">· {{ $svc->unit_label }}</span>
            </div>
            <div class="flex gap-2">
                <button onclick="openEditService('{{ $svc->id }}','{{ addslashes($svc->service_name) }}','{{ $svc->service_type }}','{{ addslashes($svc->unit_label) }}')"
                    class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-3 py-1.5 rounded">
                    <i class="fa-solid fa-pen"></i> Edit
                </button>
                <form method="POST" action="{{ route('guide-languages.services.destroy', [$guideLanguage, $svc]) }}"
                      onsubmit="return confirm('Delete {{ $svc->service_name }}?')">
                    @csrf @method('DELETE')
                    <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-3 py-1.5 rounded">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Tiers table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                    <th class="text-left px-5 py-2">Min Pax</th>
                    <th class="text-left px-5 py-2">Max Pax</th>
                    <th class="text-right px-5 py-2">Rate / Unit ({{ $guideLanguage->currency }})</th>
                    <th class="text-right px-5 py-2">Per Pax (10 pax)</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($svc->tiers as $tier)
                <tr class="border-b border-gray-50 last:border-0">
                    <td class="px-5 py-2">{{ $tier->min_pax }}</td>
                    <td class="px-5 py-2">{{ $tier->max_pax ?? '∞' }}</td>
                    <td class="px-5 py-2 text-right font-semibold">{{ number_format($tier->rate, 0, ',', '.') }}</td>
                    <td class="px-5 py-2 text-right text-green-600 font-semibold">{{ number_format($tier->rate / 10, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right">
                        <form method="POST" action="{{ route('guide-languages.tiers.destroy', [$guideLanguage, $tier]) }}"
                              onsubmit="return confirm('Delete this tier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded hover:bg-red-50">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Add tier inline --}}
        <div class="border-t border-gray-100 px-5 py-3 bg-gray-50">
            <form method="POST" action="{{ route('guide-languages.tiers.store', [$guideLanguage, $svc]) }}"
                  class="flex items-center gap-2">
                @csrf
                <span class="text-xs text-gray-400 font-semibold mr-2">+ Add Tier:</span>
                <input type="number" name="min_pax" placeholder="Min pax" min="1" required
                    class="border border-gray-300 rounded px-2 py-1 text-xs w-24 focus:outline-none focus:ring-1 focus:ring-blue-400">
                <input type="number" name="max_pax" placeholder="Max (∞)"
                    class="border border-gray-300 rounded px-2 py-1 text-xs w-24 focus:outline-none focus:ring-1 focus:ring-blue-400">
                <input type="number" name="rate" placeholder="Rate" required min="0"
                    class="border border-gray-300 rounded px-2 py-1 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-blue-400">
                <button type="submit"
                    class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-3 py-1.5 rounded">
                    Add
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <i class="fa-solid fa-list text-5xl mb-3 block"></i>
        <p>No services yet.</p>
        <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')"
            class="text-blue-600 hover:underline mt-1">Add one</button>
    </div>
    @endforelse
</div>

{{-- Edit Info Modal --}}
<div id="editInfoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800">Edit Language Info</h3>
            <button onclick="document.getElementById('editInfoModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('guide-languages.update-info', $guideLanguage) }}">
            @csrf @method('PUT')
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Language Name</label>
                    <input type="text" name="language_name" value="{{ $guideLanguage->language_name }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Destination</label>
                    <input type="text" name="destination" value="{{ $guideLanguage->destination }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. Bali">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Currency</label>
                    <select name="currency" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @foreach(['IDR','USD','SGD','MYR','EUR'] as $c)
                            <option value="{{ $c }}" {{ $guideLanguage->currency===$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes</label>
                    <input type="text" name="notes" value="{{ $guideLanguage->notes }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Optional">
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editInfoModal').classList.add('hidden')"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded text-sm font-semibold">Cancel</button>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Service Modal --}}
<div id="editServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800">Edit Service</h3>
            <button onclick="document.getElementById('editServiceModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="editServiceForm" method="POST">
            @csrf @method('PUT')
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Service Name</label>
                    <input type="text" id="edit-svc-name" name="service_name"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Service Type</label>
                    <select id="edit-svc-type" name="service_type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}">{{ \App\Models\GuideService::serviceTypeLabel($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Unit Label</label>
                    <input type="text" id="edit-svc-unit" name="unit_label"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                {{-- Hidden tiers (carry existing) --}}
                <input type="hidden" name="tiers[0][min_pax]" value="1">
                <input type="hidden" name="tiers[0][max_pax]" value="">
                <input type="hidden" name="tiers[0][rate]" value="0">
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editServiceModal').classList.add('hidden')"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded text-sm font-semibold">Cancel</button>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Service Modal --}}
<div id="addServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800 text-lg">Add Service Type</h3>
            <button onclick="document.getElementById('addServiceModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('guide-languages.services.store', $guideLanguage) }}">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Service Type</label>
                        <select name="service_type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            @foreach($serviceTypes as $type)
                                <option value="{{ $type }}">{{ \App\Models\GuideService::serviceTypeLabel($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Service Name</label>
                        <input type="text" name="service_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Unit Label</label>
                        <input type="text" name="unit_label" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="per trip / per hari">
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Pax Tiers & Rates</label>
                        <button type="button" onclick="addTier()"
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded">+ Add Tier</button>
                    </div>
                    <table class="w-full text-sm border border-gray-200 rounded overflow-hidden">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="text-left px-3 py-2">Min Pax</th>
                                <th class="text-left px-3 py-2">Max Pax</th>
                                <th class="text-left px-3 py-2">Rate / Unit ({{ $guideLanguage->currency }})</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tiersBody">
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2"><input type="number" name="tiers[0][min_pax]" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm" value="1" min="1" required></td>
                                <td class="px-3 py-2"><input type="number" name="tiers[0][max_pax]" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="∞"></td>
                                <td class="px-3 py-2"><input type="number" name="tiers[0][rate]" class="w-32 border border-gray-300 rounded px-2 py-1 text-sm text-right" value="0" required></td>
                                <td class="px-2"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600">×</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addServiceModal').classList.add('hidden')"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded text-sm font-semibold">Cancel</button>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold">Save Service</button>
            </div>
        </form>
    </div>
</div>

<script>
let tierIdx = 1;
function addTier() {
    const i = tierIdx++;
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100';
    tr.innerHTML = `
        <td class="px-3 py-2"><input type="number" name="tiers[${i}][min_pax]" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm" value="1" min="1" required></td>
        <td class="px-3 py-2"><input type="number" name="tiers[${i}][max_pax]" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="∞"></td>
        <td class="px-3 py-2"><input type="number" name="tiers[${i}][rate]" class="w-32 border border-gray-300 rounded px-2 py-1 text-sm text-right" value="0" required></td>
        <td class="px-2"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600 text-lg">×</button></td>
    `;
    document.getElementById('tiersBody').appendChild(tr);
}

function openEditService(id, name, type, unit) {
    const base = '{{ route("guide-languages.show", $guideLanguage) }}';
    document.getElementById('editServiceForm').action = '/guide-languages/{{ $guideLanguage->id }}/services/' + id;
    document.getElementById('edit-svc-name').value = name;
    document.getElementById('edit-svc-type').value = type;
    document.getElementById('edit-svc-unit').value = unit;
    document.getElementById('editServiceModal').classList.remove('hidden');
}
</script>
@endsection
