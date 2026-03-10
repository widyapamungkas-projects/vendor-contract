@extends('layouts.app')
@section('title', 'Guide Fee')
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Guide Fee</h2>
            <p class="text-sm text-gray-500">Manage guide rates by destination & language</p>
        </div>
        <a href="{{ route('guide-languages.create') }}"
           class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add Language
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($languages->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-person-chalkboard text-5xl mb-3 block"></i>
            <p class="mb-2">No guide languages yet.</p>
            <a href="{{ route('guide-languages.create') }}" class="text-blue-600 hover:underline">Add one</a>
        </div>
    @else

    @php
        // Group by destination
        $grouped = $languages->groupBy(fn($l) => $l->destination ?: '(No Destination)');
        $typeColors = [
            'airport_transfer' => 'bg-blue-100 text-blue-700',
            'full_day'         => 'bg-green-100 text-green-700',
            'half_day'         => 'bg-yellow-100 text-yellow-700',
            'overtime'         => 'bg-purple-100 text-purple-700',
            'tipping'          => 'bg-red-100 text-red-700',
            'package'          => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    @foreach($grouped as $destination => $langs)
    {{-- DESTINATION GROUP --}}
    <div class="mb-5">
        {{-- Destination header --}}
        <div class="flex items-center gap-3 mb-2 px-1">
            <i class="fa-solid fa-location-dot text-indigo-500"></i>
            <h3 class="font-bold text-indigo-700 text-base uppercase tracking-wide">{{ $destination }}</h3>
            <span class="text-gray-400 text-xs">{{ $langs->count() }} language(s)</span>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @foreach($langs as $lang)
            @php $isLastLang = $loop->last; @endphp

            {{-- LANGUAGE ROW --}}
            <div class="{{ !$isLastLang ? 'border-b border-gray-200' : '' }}">
                <div class="flex items-center justify-between px-5 py-3 cursor-pointer hover:bg-gray-50 select-none bg-gray-50"
                     onclick="toggleLang('{{ $lang->id }}')">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chevron-right text-gray-400 text-xs transition-transform duration-200" id="chev-{{ $lang->id }}"></i>
                        <i class="fa-solid fa-language text-blue-500"></i>
                        <span class="font-semibold text-gray-800">{{ $lang->language_name }}</span>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded">{{ $lang->currency }}</span>
                        <span class="text-gray-400 text-xs">{{ $lang->services->count() }} service(s)</span>
                        @if($lang->notes)
                            <span class="text-gray-400 text-xs">· {{ $lang->notes }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                        <a href="{{ route('guide-languages.show', $lang) }}"
                           class="text-xs bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 font-semibold px-3 py-1.5 rounded">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('guide-languages.destroy', $lang) }}"
                              onsubmit="return confirm('Delete {{ $lang->language_name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-white hover:bg-red-50 text-red-500 border border-red-200 font-semibold px-3 py-1.5 rounded">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- SERVICES ACCORDION --}}
                <div id="lang-{{ $lang->id }}" class="hidden">
                    @if($lang->services->isEmpty())
                        <div class="px-10 py-3 text-sm text-gray-400 italic border-t border-gray-100">
                            No services. <a href="{{ route('guide-languages.show', $lang) }}" class="text-blue-600 hover:underline">Add service →</a>
                        </div>
                    @else
                        @foreach($lang->services as $svc)
                        @php $isLastSvc = $loop->last; @endphp
                        <div class="border-t border-gray-100 {{ !$isLastSvc ? '' : '' }}">
                            {{-- Service toggle --}}
                            <div class="flex items-center justify-between px-10 py-2.5 cursor-pointer hover:bg-blue-50 select-none"
                                 onclick="toggleSvc('{{ $svc->id }}')">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-chevron-right text-gray-300 text-xs transition-transform duration-200" id="chev-svc-{{ $svc->id }}"></i>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded {{ $typeColors[$svc->service_type] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ \App\Models\GuideService::serviceTypeLabel($svc->service_type) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">{{ $svc->service_name }}</span>
                                    <span class="text-gray-400 text-xs">· {{ $svc->unit_label }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $svc->tiers->count() }} tier(s)</span>
                            </div>

                            {{-- Tier rates --}}
                            <div id="svc-{{ $svc->id }}" class="hidden bg-white">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-xs text-gray-400 uppercase tracking-wide bg-gray-50">
                                            <th class="text-left px-14 py-2">Min Pax</th>
                                            <th class="text-left px-4 py-2">Max Pax</th>
                                            <th class="text-right px-5 py-2">Rate / Unit ({{ $lang->currency }})</th>
                                            <th class="text-right px-5 py-2">Per Pax (10 pax)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($svc->tiers as $tier)
                                        <tr class="border-t border-gray-100">
                                            <td class="px-14 py-2 text-gray-600">{{ $tier->min_pax }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $tier->max_pax ?? '∞' }}</td>
                                            <td class="px-5 py-2 text-right font-semibold text-gray-800">{{ number_format($tier->rate, 0, ',', '.') }}</td>
                                            <td class="px-5 py-2 text-right font-semibold text-green-600">{{ number_format($tier->rate / 10, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                        <div class="border-t border-gray-100 px-10 py-2 bg-gray-50">
                            <a href="{{ route('guide-languages.show', $lang) }}" class="text-xs text-blue-600 hover:underline">
                                <i class="fa-solid fa-pen-to-square"></i> Manage services & rates →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @endif
</div>

<script>
function toggleLang(id) {
    const el = document.getElementById('lang-' + id);
    const chev = document.getElementById('chev-' + id);
    const open = !el.classList.contains('hidden');
    el.classList.toggle('hidden');
    chev.style.transform = open ? '' : 'rotate(90deg)';
}
function toggleSvc(id) {
    const el = document.getElementById('svc-' + id);
    const chev = document.getElementById('chev-svc-' + id);
    const open = !el.classList.contains('hidden');
    el.classList.toggle('hidden');
    chev.style.transform = open ? '' : 'rotate(90deg)';
}
</script>
@endsection
