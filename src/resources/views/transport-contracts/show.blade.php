@extends('layouts.app')
@section('title', $contract->contract_code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('transport-contracts.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $contract->contract_code }}</h1>
        @php
            $statusColor = match($contract->status) {
                'active'        => 'bg-green-100 text-green-700',
                'expiring_soon' => 'bg-yellow-100 text-yellow-700',
                'expired'       => 'bg-red-100 text-red-700',
                default         => 'bg-gray-100 text-gray-700',
            };
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
            {{ __('contracts.' . $contract->status) }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('transport-contracts.edit', $contract) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 flex items-center gap-2">
            <i class="fa-solid fa-pen"></i> {{ __('contracts.edit') }}
        </a>
        <form method="POST" action="{{ route('transport-contracts.destroy', $contract) }}"
              onsubmit="return confirm('{{ __('contracts.confirm_delete') }}')">
            @csrf @method('DELETE')
            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> {{ __('contracts.delete') }}
            </button>
        </form>
    </div>
</div>

{{-- Info --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-bus mr-2 text-blue-600"></i>{{ __('contracts.vendor_name') }}
        </h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Vendor</dt><dd class="font-medium">{{ $contract->vendor_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">City</dt><dd>{{ $contract->vendor_city }}, {{ $contract->vendor_country }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Category</dt><dd>{{ $contract->price_category }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Currency</dt><dd>{{ $contract->currency }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Valid From</dt><dd>{{ $contract->valid_from->format('d-M-Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Valid Until</dt><dd>{{ $contract->valid_until->format('d-M-Y') }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-user mr-2 text-blue-600"></i>{{ __('contracts.pic_name') }}
        </h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Name</dt><dd class="font-medium">{{ $contract->pic_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $contract->pic_phone ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $contract->pic_email ?? '-' }}</dd></div>
            @if($contract->notes)
            <div class="pt-2 border-t"><dt class="text-gray-500 mb-1">Notes</dt><dd>{{ $contract->notes }}</dd></div>
            @endif
        </dl>
    </div>
</div>

{{-- Vehicles & Rates --}}
@foreach($contract->vehicles as $vehicle)
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center gap-3 mb-4 border-b pb-2">
        <span class="px-2 py-1 rounded text-xs font-medium
            {{ $vehicle->category == 'car' ? 'bg-blue-100 text-blue-700' : ($vehicle->category == 'van' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700') }}">
            {{ ucfirst($vehicle->category) }}
        </span>
        <h2 class="font-semibold text-gray-700">{{ $vehicle->vehicle_name }}</h2>
        @if($vehicle->capacity)
            <span class="text-sm text-gray-500"><i class="fa-solid fa-users mr-1"></i>{{ $vehicle->capacity }} seat</span>
        @endif
        @if($vehicle->brand)
            <span class="text-sm text-gray-500">— {{ $vehicle->brand }}</span>
        @endif
    </div>

    @if($vehicle->rates->count())
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.route_name') }}</th>
                <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.route_type') }}</th>
                <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.duration') }}</th>
                <th class="px-3 py-2 text-right font-semibold">{{ __('contracts.price') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($vehicle->rates as $rate)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-medium">{{ $rate->route_name }}</td>
                <td class="px-3 py-2">
                    <span class="px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700">
                        {{ __('contracts.' . $rate->route_type) }}
                    </span>
                </td>
                <td class="px-3 py-2 text-gray-600">{{ $rate->duration ?? '-' }}</td>
                <td class="px-3 py-2 text-right font-semibold text-green-700">
                    {{ $contract->currency }} {{ number_format($rate->price, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p class="text-sm text-gray-400 text-center py-4">No routes added.</p>
    @endif
</div>
@endforeach

@endsection
```

---

Simpan semua file, buka browser:
```
http://100.72.79.110:8080/transport-contracts