@extends('layouts.app')
@section('title','Tour Costing')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tour Package Costing</h2>
            <p class="text-sm text-gray-500">Manage tour package cost calculations</p>
        </div>
        <a href="{{ route('tour-packages.create') }}"
           class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Package
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($packages->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <i class="fa-solid fa-calculator text-5xl mb-3 block"></i>
            <p>No packages yet. <a href="{{ route('tour-packages.create') }}" class="text-blue-600 hover:underline">Create one</a></p>
        </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="text-left px-5 py-3">Code</th>
                    <th class="text-left px-4 py-3">Agent</th>
                    <th class="text-left px-4 py-3">Destination</th>
                    <th class="text-left px-4 py-3">Period</th>
                    <th class="text-center px-4 py-3">Pax</th>
                    <th class="text-left px-4 py-3">Currency</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($packages as $pkg)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs font-semibold text-blue-700">{{ $pkg->package_code }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $pkg->agent }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pkg->destination ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        @if($pkg->period_from)
                            {{ $pkg->period_from->format('d M Y') }} → {{ $pkg->period_to?->format('d M Y') ?? '?' }}
                        @else —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">{{ $pkg->pax }}</td>
                    <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded">{{ $pkg->currency }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('tour-packages.show', $pkg) }}"
                               class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded font-semibold">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('tour-packages.edit', $pkg) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded font-semibold">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('tour-packages.destroy', $pkg) }}"
                                  onsubmit="return confirm('Delete {{ $pkg->package_code }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded font-semibold">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $packages->links() }}</div>
    </div>
    @endif
</div>
@endsection
