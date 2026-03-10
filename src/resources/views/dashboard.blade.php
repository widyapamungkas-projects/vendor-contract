@extends("layouts.app")
@section("title", "Dashboard")
@section("content")

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-gauge-high mr-2 text-blue-700"></i>Dashboard
    </h1>
    <p class="text-gray-500 text-sm mt-1">Ringkasan status kontrak semua modul</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($modules as $key => $module)
    @php
        $colors = [
            "blue"   => ["bg" => "bg-blue-700",   "light" => "bg-blue-50",   "text" => "text-blue-700",   "border" => "border-blue-200"],
            "yellow" => ["bg" => "bg-yellow-500", "light" => "bg-yellow-50", "text" => "text-yellow-700", "border" => "border-yellow-200"],
            "green"  => ["bg" => "bg-green-600",  "light" => "bg-green-50",  "text" => "text-green-700",  "border" => "border-green-200"],
            "purple" => ["bg" => "bg-purple-600", "light" => "bg-purple-50", "text" => "text-purple-700", "border" => "border-purple-200"],
            "red"    => ["bg" => "bg-red-600",    "light" => "bg-red-50",    "text" => "text-red-700",    "border" => "border-red-200"],
        ];
        $c = $colors[$module["color"]];
    @endphp
    <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow border {{ $c["border"] }} overflow-hidden">
        {{-- Header --}}
        <div class="{{ $c["bg"] }} px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fa-solid {{ $module["icon"] }} text-white text-lg"></i>
                </div>
                <h2 class="text-white font-semibold text-lg">{{ $module["label"] }}</h2>
            </div>
            <span class="bg-white/20 text-white text-sm font-bold px-3 py-1 rounded-full">
                {{ $module["stats"]["total"] }} total
            </span>
        </div>

        {{-- Stats --}}
        <div class="px-5 py-4 grid grid-cols-3 gap-3">
            <div class="{{ $c["light"] }} rounded-lg p-3 text-center">
                <div class="text-2xl font-bold {{ $c["text"] }}">{{ $module["stats"]["active"] }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="fa-solid fa-circle-check mr-1 text-green-500"></i>Aktif
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $module["stats"]["expiring_soon"] }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="fa-solid fa-clock mr-1 text-yellow-500"></i>Segera Exp
                </div>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $module["stats"]["expired"] }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="fa-solid fa-circle-xmark mr-1 text-red-500"></i>Expired
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 pb-4">
            <a href="{{ route($module["route"]) }}"
               class="block w-full text-center {{ $c["bg"] }} text-white text-sm py-2 rounded-lg hover:opacity-90 transition">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection
