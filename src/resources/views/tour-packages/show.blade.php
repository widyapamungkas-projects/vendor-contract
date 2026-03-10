@extends('layouts.app')
@section('title', $tourPackage->package_code)
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('tour-packages.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Tour Costing</a>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $tourPackage->package_code }}</h2>
            <p class="text-sm text-gray-500">{{ $tourPackage->agent }} · {{ $tourPackage->destination }} · {{ $tourPackage->pax }} pax · {{ $tourPackage->currency }}</p>
        </div>
        <a href="{{ route('tour-packages.edit', $tourPackage) }}"
           class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm font-semibold">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
    </div>

    @php
        $pax = max($tourPackage->pax, 1);
        $laTotal = $tourPackage->laCosts->sum('amount');
        $laPerPax = $laTotal / $pax;
        $itinPerPax = $tourPackage->itinerary->sum('price_per_pax');
        $totalLA = $laPerPax + $itinPerPax;
        $cfg = $tourPackage->roomConfig;
        $conv = $tourPackage->getConversionRate();
        $fmt = fn($n) => number_format(round($n), 0, ',', '.');
    @endphp

    {{-- Summary cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4 border-l-4 border-l-blue-600">
            <div class="text-xs text-gray-400 uppercase font-bold tracking-wide mb-1">LA per Group</div>
            <div class="text-xl font-black text-blue-700">IDR {{ $fmt($laTotal) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $fmt($laPerPax) }} / pax</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 border-l-4 border-l-teal-600">
            <div class="text-xs text-gray-400 uppercase font-bold tracking-wide mb-1">Itinerary / Pax</div>
            <div class="text-xl font-black text-teal-700">IDR {{ $fmt($itinPerPax) }}</div>
            <div class="text-xs text-gray-400 mt-1">entrance + activity + resto</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 border-l-4 border-l-purple-600">
            <div class="text-xs text-gray-400 uppercase font-bold tracking-wide mb-1">Total LA / Pax</div>
            <div class="text-xl font-black text-purple-700">IDR {{ $fmt($totalLA) }}</div>
            <div class="text-xs text-gray-400 mt-1">before hotel</div>
        </div>
    </div>

    {{-- Hotel results --}}
    @foreach($tourPackage->hotels as $hotel)
    @php
        $roomCost = $hotel->room_rate * $hotel->nights * (($cfg->sgl??0)+($cfg->twn??0)+($cfg->trp??0)+($cfg->foc??0));
        $roomPerPax = $roomCost / $pax;
        $twnSell = $totalLA + $roomPerPax + ($cfg->margin_twn ?? 0);
        $sglSell = $twnSell * 1.5;
        $trpSell = $twnSell * 0.85;
        $rev = ($cfg->sgl??0)*$sglSell + ($cfg->twn??0)*$twnSell*2 + ($cfg->trp??0)*$trpSell*3;
        $cost = $laTotal*$pax + $roomCost;
        $profit = $rev - $cost;
        $pct = $rev > 0 ? $profit/$rev*100 : 0;
        $green = $profit >= 0;
    @endphp
    <div class="bg-white rounded-lg border border-gray-200 mb-4 overflow-hidden">
        <div class="bg-gray-800 px-5 py-3 flex justify-between items-center">
            <span class="text-white font-bold">{{ $hotel->hotel_name }}{{ $hotel->room_type ? ' — '.$hotel->room_type : '' }}</span>
            <span class="text-gray-400 text-xs">IDR {{ $fmt($hotel->room_rate) }}/malam · {{ $hotel->nights }} malam</span>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach([['SINGLE',$sglSell,$sglSell/$conv],['TWIN',$twnSell,$twnSell/$conv],['TRIPLE',$trpSell,$trpSell/$conv]] as [$lbl,$idr,$fx])
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                    <div class="text-xs text-gray-400 font-bold tracking-widest mb-2">{{ $lbl }}</div>
                    <div class="text-lg font-black text-gray-800">IDR {{ $fmt($idr) }}</div>
                    @if($tourPackage->currency !== 'IDR')
                        <div class="text-sm text-green-600 font-bold mt-1">{{ $tourPackage->currency }} {{ $fmt($fx) }}</div>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="rounded-lg p-4 {{ $green ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <div class="text-xs font-bold uppercase tracking-widest {{ $green ? 'text-green-700' : 'text-red-700' }} mb-3">P&L Summary</div>
                <div class="grid grid-cols-4 gap-3">
                    @foreach([['Revenue','IDR '.$fmt($rev)],['Cost','IDR '.$fmt($cost)],['Profit','IDR '.$fmt($profit)],['Margin',$fmt($pct).'%']] as [$l,$v])
                    <div>
                        <div class="text-xs text-gray-500 mb-1">{{ $l }}</div>
                        <div class="font-bold text-sm {{ $green ? 'text-green-700' : 'text-red-700' }}">{{ $v }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
