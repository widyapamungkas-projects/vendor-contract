@extends('layouts.app')
@section('title', 'Payment Slips')
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fa-solid fa-file-invoice-dollar mr-2 text-green-600"></i>Payment Slips
        </h1>
        @if($unreadCount > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }} new</span>
        @endif
    </div>
    <div class="flex gap-2 items-center">
        @if($unreadCount > 0)
        <button onclick="markAllRead()"
                class="text-xs text-blue-600 px-3 py-1.5 border border-blue-200 rounded hover:bg-blue-50">
            <i class="fa-solid fa-check-double mr-1"></i>Mark all read
        </button>
        @endif
        <form method="GET" action="{{ route('payment-slips.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search file..."
                   class="border rounded px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <button class="bg-gray-100 px-3 py-1.5 rounded text-sm hover:bg-gray-200">
                <i class="fa-solid fa-search"></i>
            </button>
            @if($search)
            <a href="{{ route('payment-slips.index') }}"
               class="bg-gray-100 px-3 py-1.5 rounded text-sm hover:bg-gray-200 text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Search Results --}}
@if($search)
<div class="bg-white rounded shadow overflow-hidden mb-6">
    <div class="px-4 py-3 bg-gray-50 border-b">
        <span class="text-sm font-semibold text-gray-600">
            Search results for "{{ $search }}" — {{ count($searchResults) }} file(s)
        </span>
    </div>
    @if(count($searchResults) > 0)
        @include('payment-slips._file_table', ['files' => $searchResults])
    @else
        <div class="p-8 text-center text-gray-400 text-sm">No files found.</div>
    @endif
</div>

@else

{{-- ── LATEST FILES ── --}}
<div class="bg-white rounded shadow overflow-hidden mb-6">
    {{-- Big title header --}}
    <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-500 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-xs font-medium uppercase tracking-wider mb-1">Payment Slip</p>
                @php
                    $dateLabel = !empty($latestFolderPath) ? collect($latestFolderPath)->pluck('name')->implode(' / ') : 'Today';
                @endphp
                <h2 class="text-2xl font-bold">Updated : {{ $latestUpdatedAt }}</h2>
            </div>
            <i class="fa-solid fa-file-invoice-dollar text-5xl text-green-300 opacity-50"></i>
        </div>
    </div>

    @if(count($latestFiles) > 0)
        @include('payment-slips._file_table', ['files' => $latestFiles])
    @else
        <div class="p-12 text-center">
            <i class="fa-solid fa-folder-open text-5xl text-gray-200 mb-3"></i>
            <p class="text-gray-500">No files in the latest folder.</p>
        </div>
    @endif
</div>

{{-- ── ARCHIVE FOLDERS ── --}}
@if(count($yearFolders) > 0)
<div class="mb-2">
    <h2 class="text-lg font-bold text-gray-700 mb-3">
        <i class="fa-solid fa-box-archive mr-2 text-gray-400"></i>Other Payment Slip
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($yearFolders as $folder)
        <a href="{{ route('payment-slips.archive', ['folder' => $folder->getId()]) }}"
           class="bg-white rounded shadow p-4 flex flex-col items-center gap-2 hover:bg-yellow-50 hover:shadow-md transition group">
            <i class="fa-solid fa-folder text-4xl text-yellow-400 group-hover:text-yellow-500"></i>
            <span class="text-xs font-semibold text-gray-700 text-center">{{ $folder->getName() }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

@endif

<script>
function markAllRead() {
    fetch('{{ route('payment-slips.mark-read') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(() => window.location.reload());
}
</script>
@endsection
