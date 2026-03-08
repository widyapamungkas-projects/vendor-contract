@extends('layouts.app')
@section('title', $contract->contract_code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hotel-contracts.index') }}" class="text-gray-500 hover:text-gray-700">
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
        <a href="{{ route('hotel-contracts.edit', $contract) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 flex items-center gap-2">
            <i class="fa-solid fa-pen"></i> {{ __('contracts.edit') }}
        </a>
        <form method="POST" action="{{ route('hotel-contracts.destroy', $contract) }}"
              onsubmit="return confirm('{{ __('contracts.confirm_delete') }}')">
            @csrf @method('DELETE')
            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> {{ __('contracts.delete') }}
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

    {{-- Basic Info --}}
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-hotel mr-2 text-blue-600"></i>{{ __('contracts.hotel_name') }}
        </h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.hotel_name') }}</dt>
                <dd class="font-medium">{{ $contract->hotel_name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.hotel_city') }}</dt>
                <dd>{{ $contract->destination ?? "-" }}{{ $contract->area ? " (".$contract->area.")" : "" }}, {{ $contract->hotel_country }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.contract_type') }}</dt>
                <dd>{{ __('contracts.' . $contract->contract_type) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.price_category') }}</dt>
                <dd>{{ $contract->price_category }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.currency') }}</dt>
                <dd>{{ $contract->currency }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.valid_from') }}</dt>
                <dd>{{ $contract->valid_from->format('d-M-Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.valid_until') }}</dt>
                <dd>{{ $contract->valid_until->format('d-M-Y') }}</dd>
            </div>
        </dl>
    </div>

    {{-- PIC Info --}}
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-user mr-2 text-blue-600"></i>{{ __('contracts.pic_name') }}
        </h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.pic_name') }}</dt>
                <dd class="font-medium">{{ $contract->pic_name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.pic_phone') }}</dt>
                <dd>{{ $contract->pic_phone ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('contracts.pic_email') }}</dt>
                <dd>{{ $contract->pic_email ?? '-' }}</dd>
            </div>
            @if($contract->notes)
            <div class="pt-2 border-t">
                <dt class="text-gray-500 mb-1">{{ __('contracts.notes') }}</dt>
                <dd class="text-gray-700">{{ $contract->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>

{{-- Room Rates --}}
@if($contract->roomRates->count())
<div class="bg-white rounded shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
        <i class="fa-solid fa-bed mr-2 text-blue-600"></i>{{ __('contracts.room_rates') }}
    </h2>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.room_type') }}</th>
                <th class="px-3 py-2 text-right font-semibold">{{ __('contracts.rate') }}</th>
                <th class="px-3 py-2 text-right font-semibold">{{ __('contracts.extra_bed') }}</th>
                <th class="px-3 py-2 text-right font-semibold">Child & Breakfast</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($contract->roomRates as $rate)
            <tr>
                <td class="px-3 py-2 font-medium">{{ $rate->room_type }}</td>
                <td class="px-3 py-2 text-right">{{ $contract->currency }} {{ number_format($rate->rate, 0, ',', '.') }}</td>
                <td class="px-3 py-2 text-right">{{ $contract->currency }} {{ number_format($rate->extra_bed_rate, 0, ',', '.') }}</td>
                <td class="px-3 py-2 text-right">{{ $contract->currency }} {{ number_format($rate->breakfast_rate, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Season Surcharges + Blackout Dates dalam 2 kolom --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

    {{-- Season Surcharges --}}
    @if($contract->seasonSurcharges->count())
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-sun mr-2 text-yellow-500"></i>{{ __('contracts.season_surcharges') }}
        </h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.season_name') }}</th>
                    <th class="px-3 py-2 text-right font-semibold">Amount</th>
                    <th class="px-3 py-2 text-left font-semibold">Period</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($contract->seasonSurcharges as $surcharge)
                <tr>
                    <td class="px-3 py-2 font-medium">{{ $surcharge->season_name }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-orange-600">
                        {{ $contract->currency }} {{ number_format($surcharge->surcharge_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-3 py-2 text-xs text-gray-600">
                        {{ $surcharge->start_date->format('d-M') }} – {{ $surcharge->end_date->format('d-M-Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Blackout Dates --}}
    @if($contract->blackoutDates->count())
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-calendar-xmark mr-2 text-red-500"></i>{{ __('contracts.blackout_dates') }}
        </h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.valid_from') }}</th>
                    <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.valid_until') }}</th>
                    <th class="px-3 py-2 text-left font-semibold">{{ __('contracts.reason') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($contract->blackoutDates as $blackout)
                <tr class="bg-red-50">
                    <td class="px-3 py-2 text-red-700 font-bold">{{ $blackout->start_date->format('d-M-Y') }}</td>
                    <td class="px-3 py-2 text-red-700 font-bold">{{ $blackout->end_date->format('d-M-Y') }}</td>
                    <td class="px-3 py-2 text-red-700 font-bold">{{ $blackout->reason ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
@endsection