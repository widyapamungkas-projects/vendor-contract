@extends('layouts.app')
@section('title', __('contracts.activity_title'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-person-hiking mr-2 text-blue-700"></i>
        {{ __('contracts.activity_title') }}
    </h1>
    <a href="{{ route('activity-contracts.create') }}"
       class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> {{ __('contracts.activity_create') }}
    </a>
</div>

<form method="GET" class="bg-white rounded shadow p-4 mb-6 flex gap-3 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="{{ __('contracts.search') }}..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-48">
    <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Destination..." onchange="this.form.submit()" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm w-40">
    <select name="status" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">{{ __('contracts.all_status') }}</option>
        <option value="active"        {{ request('status') == 'active'        ? 'selected' : '' }}>{{ __('contracts.active') }}</option>
        <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>{{ __('contracts.expiring_soon') }}</option>
        <option value="expired"       {{ request('status') == 'expired'       ? 'selected' : '' }}>{{ __('contracts.expired') }}</option>
    </select>
    <button class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
        <i class="fa-solid fa-search mr-1"></i>{{ __('contracts.search') }}
    </button>
    @if(request()->hasAny(['search','status','destination']))
        <a href="{{ route('activity-contracts.index') }}" class="px-4 py-2 rounded text-sm border hover:bg-gray-50">
            <i class="fa-solid fa-xmark mr-1"></i>Reset
        </a>
    @endif
</form>


@php
    $allDestinations = \App\Models\ActivityContract::whereNotNull("destination")
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
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.contract_code') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.vendor_name') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.hotel_city') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.activity_items') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.valid_until') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.status') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($contracts as $contract)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $contract->contract_code }}</td>
                <td class="px-4 py-3 font-medium">{{ $contract->vendor_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->vendor_city }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->items->count() }} activity</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->valid_until->format('d-M-Y') }}</td>
                <td class="px-4 py-3">
                    @php
                        $statusColor = match($contract->status) {
                            'active'        => 'bg-green-100 text-green-700',
                            'expiring_soon' => 'bg-yellow-100 text-yellow-700',
                            'expired'       => 'bg-red-100 text-red-700',
                            default         => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColor }}">
                        {{ __('contracts.' . $contract->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('activity-contracts.show', $contract) }}"
                           class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('activity-contracts.edit', $contract) }}"
                           class="text-yellow-600 hover:text-yellow-800"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('activity-contracts.destroy', $contract) }}"
                              onsubmit="return confirm('{{ __('contracts.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                    {{ __('contracts.no_data') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($contracts->hasPages())
        <div class="px-4 py-3 border-t">{{ $contracts->withQueryString()->links() }}</div>
    @endif
</div>

<script>
function filterDestination(dest) {
    // Update pill aktif
    document.querySelectorAll(".dest-pill").forEach(function(btn) {
        if (btn.dataset.dest === dest) {
            btn.classList.add("bg-blue-700", "text-white", "border-blue-700");
            btn.classList.remove("bg-white", "text-gray-600", "hover:bg-gray-50");
        } else {
            btn.classList.remove("bg-blue-700", "text-white", "border-blue-700");
            btn.classList.add("bg-white", "text-gray-600", "hover:bg-gray-50");
        }
    });

    // Build URL dengan query params yang ada
    const url = new URL(window.location.href);
    if (dest) {
        url.searchParams.set("destination", dest);
    } else {
        url.searchParams.delete("destination");
    }
    url.searchParams.delete("page");

    // Update browser URL tanpa reload
    window.history.pushState({}, "", url.toString());

    // Fetch tabel baru
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