@extends('layouts.app')
@section('title', __('contracts.title'))
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-hotel mr-2 text-blue-700"></i>
        {{ __('contracts.title') }}
    </h1>
    <a href="{{ route('hotel-contracts.create') }}"
       class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> {{ __('contracts.create') }}
    </a>
</div>

{{-- FILTER --}}
<form method="GET" class="bg-white rounded shadow p-4 mb-6 flex gap-3 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="{{ __('contracts.search') }}..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-40">
    <input type="text" name="destination" value="{{ request('destination') }}"
           placeholder="Destination (e.g. Bali)"
           class="border rounded px-3 py-2 text-sm w-44">
    <input type="text" name="area" value="{{ request('area') }}"
           placeholder="Area (e.g. Kuta)"
           class="border rounded px-3 py-2 text-sm w-40">
    <select name="status" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">{{ __('contracts.all_status') }}</option>
        <option value="active"        {{ request('status') == 'active'        ? 'selected' : '' }}>{{ __('contracts.active') }}</option>
        <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>{{ __('contracts.expiring_soon') }}</option>
        <option value="expired"       {{ request('status') == 'expired'       ? 'selected' : '' }}>{{ __('contracts.expired') }}</option>
    </select>
    <select name="contract_type" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">All Type</option>
        <option value="regular"  {{ request('contract_type') == 'regular'  ? 'selected' : '' }}>{{ __('contracts.regular') }}</option>
        <option value="campaign" {{ request('contract_type') == 'campaign' ? 'selected' : '' }}>{{ __('contracts.campaign') }}</option>
    </select>
    <button class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
        <i class="fa-solid fa-search mr-1"></i>{{ __('contracts.search') }}
    </button>
    @if(request()->hasAny(['search','status','contract_type','destination','area']))
    <a href="{{ route('hotel-contracts.index') }}" class="px-4 py-2 rounded text-sm border hover:bg-gray-50">
        <i class="fa-solid fa-xmark mr-1"></i>Reset
    </a>
    @endif
</form>

{{-- TABLE --}}
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.hotel_name') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Destination</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Area</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.contract_type') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.valid_until') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.status') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('contracts.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($contracts as $contract)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $contract->hotel_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->destination ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->area ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $contract->contract_type == 'regular' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ __('contracts.' . $contract->contract_type) }}
                    </span>
                </td>
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
                        <a href="{{ route('hotel-contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-800" title="Detail"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('hotel-contracts.edit', $contract) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('hotel-contracts.destroy', $contract) }}" onsubmit="return confirm('{{ __('contracts.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><i class="fa-solid fa-trash"></i></button>
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
@endsection