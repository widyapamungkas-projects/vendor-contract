@extends('layouts.app')
@section('title', 'Restaurant Contracts')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-utensils mr-2 text-blue-700"></i>Restaurant Contracts
    </h1>
    <a href="{{ route('restaurant-contracts.create') }}"
       class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Create Contract
    </a>
</div>
<form method="GET" class="bg-white rounded shadow p-4 mb-6 flex gap-3 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search restaurant..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-48">
    <select name="status" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="active"        {{ request('status')=='active'        ?'selected':'' }}>Active</option>
        <option value="expiring_soon" {{ request('status')=='expiring_soon' ?'selected':'' }}>Expiring Soon</option>
        <option value="expired"       {{ request('status')=='expired'       ?'selected':'' }}>Expired</option>
    </select>
    <button class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
        <i class="fa-solid fa-search mr-1"></i>Search
    </button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('restaurant-contracts.index') }}" class="px-4 py-2 rounded text-sm border hover:bg-gray-50">
            <i class="fa-solid fa-xmark mr-1"></i>Reset
        </a>
    @endif
</form>
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Contract Code</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Restaurant</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">City</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Menus</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Valid Until</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($contracts as $contract)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $contract->contract_code }}</td>
                <td class="px-4 py-3 font-medium">{{ $contract->vendor_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->vendor_city }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->menus->count() }} menu</td>
                <td class="px-4 py-3 text-gray-600">{{ $contract->valid_until->format('d-M-Y') }}</td>
                <td class="px-4 py-3">
                    @php $sc = match($contract->status) { 'active'=>'bg-green-100 text-green-700','expiring_soon'=>'bg-yellow-100 text-yellow-700','expired'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' }; @endphp
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $sc }}">{{ ucfirst(str_replace('_',' ',$contract->status)) }}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('restaurant-contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('restaurant-contracts.edit', $contract) }}" class="text-yellow-600 hover:text-yellow-800"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('restaurant-contracts.destroy', $contract) }}" onsubmit="return confirm('Hapus contract ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400"><i class="fa-solid fa-utensils text-3xl mb-2 block"></i>No data found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($contracts->hasPages())
        <div class="px-4 py-3 border-t">{{ $contracts->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
