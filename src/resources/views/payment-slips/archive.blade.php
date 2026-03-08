@extends('layouts.app')
@section('title', 'Archive - Payment Slips')
@section('content')

<div class="flex items-center gap-3 mb-4">
    <a href="{{ route('payment-slips.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-box-archive mr-2 text-gray-500"></i>Archive
    </h1>
</div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-1 text-sm mb-4 bg-white rounded shadow px-4 py-2 flex-wrap">
    @foreach($breadcrumbs as $i => $crumb)
        @if($i < count($breadcrumbs) - 1)
            <a href="{{ route('payment-slips.archive', ['folder' => $crumb['id']]) }}"
               class="text-blue-600 hover:text-blue-800 font-medium">{{ $crumb['name'] }}</a>
            <i class="fa-solid fa-chevron-right text-gray-400 text-xs mx-1"></i>
        @else
            <span class="text-gray-700 font-semibold">{{ $crumb['name'] }}</span>
        @endif
    @endforeach
</nav>

{{-- Folders --}}
@if(count($folders) > 0)
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
    @foreach($folders as $folder)
    <a href="{{ route('payment-slips.archive', ['folder' => $folder->getId()]) }}"
       class="bg-white rounded shadow p-4 flex flex-col items-center gap-2 hover:bg-yellow-50 hover:shadow-md transition group">
        <i class="fa-solid fa-folder text-4xl text-yellow-400 group-hover:text-yellow-500"></i>
        <span class="text-xs font-semibold text-gray-700 text-center w-full truncate">{{ $folder->getName() }}</span>
    </a>
    @endforeach
</div>
@endif

{{-- Files --}}
@if(count($files) > 0)
<div class="bg-white rounded shadow overflow-hidden">
    {{-- Title header --}}
    <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-500 text-white">
        @php
            $currentCrumb = end($breadcrumbs);
        @endphp
        <p class="text-gray-300 text-xs font-medium uppercase tracking-wider mb-1">Archive</p>
        <h2 class="text-xl font-bold">Payment Slip Update &mdash; {{ $currentCrumb['name'] }}</h2>
    </div>
    @include('payment-slips._file_table', ['files' => $files])
</div>
@endif

@if(count($folders) === 0 && count($files) === 0)
<div class="bg-white rounded shadow p-12 text-center">
    <i class="fa-solid fa-folder-open text-5xl text-gray-200 mb-3"></i>
    <p class="text-gray-500">Folder ini kosong.</p>
</div>
@endif
@endsection
