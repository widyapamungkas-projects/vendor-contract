@extends('layouts.app')
@section('title', 'Entrance Fee')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-ticket mr-2 text-blue-700"></i>Entrance Fee
    </h1>
    <a href="{{ route('entrance-contracts.create') }}"
       class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Ticket
    </a>
</div>

<form method="GET" class="bg-white rounded shadow p-4 mb-6 flex gap-3 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search attraction..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-48">
    <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Destination..." onchange="this.form.submit()" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm w-40">
    <select name="attraction_type" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">All Type</option>
        @foreach(['temple'=>'Temple','museum'=>'Museum','theme_park'=>'Theme Park','natural_attraction'=>'Natural Attraction','cultural_show'=>'Cultural Show','zoo_safari'=>'Zoo/Safari','other'=>'Other'] as $val => $label)
        <option value="{{ $val }}" {{ request('attraction_type')==$val?'selected':'' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
        <i class="fa-solid fa-search mr-1"></i>Search
    </button>
    @if(request()->hasAny(['search','attraction_type','destination']))
        <a href="{{ route('entrance-contracts.index') }}" class="px-4 py-2 rounded text-sm border hover:bg-gray-50">
            <i class="fa-solid fa-xmark mr-1"></i>Reset
        </a>
    @endif
</form>


@php
    $allDestinations = \App\Models\EntranceTicket::whereNotNull("destination")
        ->where("destination", "!=", "")
        ->distinct()->orderBy("destination")->pluck("destination");
@endphp
@if($allDestinations->count())
<div class="destination-navbar flex gap-2 flex-wrap mb-4" id="dest-navbar">
    <button onclick="filterDestination('')"
       class="px-3 py-1 rounded-full text-sm border dest-pill {{ !request('destination') ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}" data-dest="">
        Semua
    </button>
    @foreach($allDestinations as $dest)
    <button onclick="filterDestination('{{ $dest }}')"
       class="px-3 py-1 rounded-full text-sm border dest-pill {{ request('destination') == $dest ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}" data-dest="{{ $dest }}">
        {{ $dest }}
    </button>
    @endforeach
</div>
@endif
<div id="table-wrapper" class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Attraction</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Type</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">Adult</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">Child</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">Infant</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Notes</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($tickets as $ticket)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $ticket->attraction_name }}</td>
                <td class="px-4 py-3">
                    @php $tc = match($ticket->attraction_type) {
                        'temple'=>'bg-yellow-100 text-yellow-700',
                        'museum'=>'bg-blue-100 text-blue-700',
                        'theme_park'=>'bg-pink-100 text-pink-700',
                        'natural_attraction'=>'bg-green-100 text-green-700',
                        'cultural_show'=>'bg-purple-100 text-purple-700',
                        'zoo_safari'=>'bg-orange-100 text-orange-700',
                        default=>'bg-gray-100 text-gray-700'
                    }; @endphp
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $tc }}">
                        {{ ucfirst(str_replace('_',' ',$ticket->attraction_type)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-medium text-green-700">
                    {{ $ticket->currency }} {{ number_format($ticket->adult_price,0,',','.') }}
                </td>
                <td class="px-4 py-3 text-right text-gray-600">
                    {{ $ticket->currency }} {{ number_format($ticket->child_price,0,',','.') }}
                </td>
                <td class="px-4 py-3 text-right text-gray-600">
                    {{ $ticket->infant_price > 0 ? $ticket->currency.' '.number_format($ticket->infant_price,0,',','.') : '-' }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $ticket->notes ?? '-' }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('entrance-contracts.edit', $ticket) }}"
                           class="text-yellow-600 hover:text-yellow-800"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('entrance-contracts.destroy', $ticket) }}"
                              onsubmit="return confirm('Hapus tiket ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    <i class="fa-solid fa-ticket text-3xl mb-2 block"></i>Belum ada data tiket masuk.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($tickets->hasPages())
        <div class="px-4 py-3 border-t">{{ $tickets->withQueryString()->links() }}</div>
    @endif
</div>

<script>
function filterDestination(dest) {
    document.querySelectorAll(".dest-pill").forEach(function(btn) {
        if (btn.dataset.dest === dest) {
            btn.classList.add("bg-blue-700", "text-white", "border-blue-700");
            btn.classList.remove("bg-white", "text-gray-600", "hover:bg-gray-50");
        } else {
            btn.classList.remove("bg-blue-700", "text-white", "border-blue-700");
            btn.classList.add("bg-white", "text-gray-600", "hover:bg-gray-50");
        }
    });

    const url = new URL(window.location.href);
    if (dest) {
        url.searchParams.set("destination", dest);
    } else {
        url.searchParams.delete("destination");
    }
    url.searchParams.delete("page");
    window.history.pushState({}, "", url.toString());

    const wrapper = document.getElementById("table-wrapper");
    wrapper.style.opacity = "0.5";

    fetch(url.toString(), {
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(r => r.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        const newTable = doc.getElementById("table-wrapper");
        if (newTable) {
            wrapper.innerHTML = newTable.innerHTML;
        }
        wrapper.style.opacity = "1";
    })
    .catch(() => { wrapper.style.opacity = "1"; });
}
</script>
@endsection
