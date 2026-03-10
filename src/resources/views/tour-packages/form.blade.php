@extends('layouts.app')
@section('title', $package ? 'Edit '.$package->package_code : 'New Package')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>.search-result-item:hover{background:#f0f7ff!important}</style>
@if(session('active_tab'))
<meta name="active-tab" content="{{ session('active_tab') }}">
@endif
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <div>
            <a href="{{ route('tour-packages.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Tour Costing</a>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                {{ $package ? 'Edit '.$package->package_code : 'New Package' }}
            </h2>
        </div>
        <div class="text-xs text-gray-400 font-mono bg-gray-100 px-3 py-1.5 rounded">{{ $packageCode }}</div>
    </div>

    <form method="POST" onsubmit="syncAllProposalFields();document.getElementById('active-tab-input').value=window._activeTab||'la'" action="{{ $package ? route('tour-packages.update', $package) : route('tour-packages.store') }}" id="costingForm">
        @csrf
        <input type="hidden" name="active_tab" id="active-tab-input" value="la">
        @if($package) @method('PUT') @endif

        {{-- Tabs --}}
        <div class="flex gap-1 mb-0 bg-gray-800 rounded-t-lg px-3 pt-3">
            @foreach(['info'=>'① Package Info','la'=>'② LA Cost','hotel'=>'③ Hotel','calc'=>'④ Proposal'] as $t => $label)
            <button type="button" onclick="setTab('{{ $t }}')" id="tab-btn-{{ $t }}"
                class="tab-btn px-4 py-2 text-xs font-bold rounded-t transition-all">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- TAB 1: Package Info --}}
        <div id="tab-info" class="tab-pane bg-white rounded-b-lg rounded-tr-lg border border-gray-200 p-6">

            {{-- Row 1: Agent + Destination --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Agent / Client *</label>
                    <input type="text" name="agent" id="f-agent" value="{{ old('agent', $package?->agent) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required placeholder="e.g. Mayflower Holidays">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Destination</label>
                    <input type="text" name="destination" id="f-destination" value="{{ old('destination', $package?->destination) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. Bali">
                </div>
            </div>

            {{-- Row 2: Period From + Period To + Duration auto --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Period From</label>
                    <input type="date" name="period_from" id="f-period-from"
                        value="{{ old('period_from', $package?->period_from?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" onchange="calcDuration()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Period To</label>
                    <input type="date" name="period_to" id="f-period-to"
                        value="{{ old('period_to', $package?->period_to?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" onchange="calcDuration()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                        Duration <span class="text-indigo-400 font-normal normal-case">(otomatis)</span>
                    </label>
                    <input type="text" name="duration" id="f-duration"
                        value="{{ old('duration', $package?->duration) }}"
                        class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-500"
                        placeholder="— pilih periode dulu —" readonly>
                    <input type="hidden" id="total-days" value="{{ old('duration', $package?->duration) ? (int)preg_replace('/D.*/','',$package?->duration ?? '0') : 0 }}">
                </div>
            </div>

            {{-- Row 3: Min Pax + Actual Pax --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                        Min Pax * <span class="text-indigo-400 font-normal normal-case">(pembagi cost)</span>
                    </label>
                    <input type="number" name="pax" id="f-pax"
                        value="{{ old('pax', $package?->pax ?? 1) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1" required oninput="recalc()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                        Actual Pax <span class="text-indigo-400 font-normal normal-case">(P&L)</span>
                    </label>
                    <input type="number" name="actual_pax" id="f-actual-pax"
                        value="{{ old('actual_pax', $package?->actual_pax) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1" oninput="recalc()" placeholder="Opsional">
                </div>
            </div>



            <div class="mt-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ old('notes', $package?->notes) }}</textarea>
            </div>
        </div>

        {{-- TAB 2: LA Cost --}}
        <div id="tab-la" class="tab-pane hidden bg-white rounded-b-lg rounded-tr-lg border border-gray-200">
            <div class="p-5 grid grid-cols-2 gap-0" style="grid-template-columns:1fr 1fr">

                {{-- LEFT: Fixed Cost --}}
                <div class="border-r border-gray-200 pr-5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                                <th class="text-left py-2 font-semibold">Item</th>
                                <th class="w-5"></th>
                                <th class="text-right py-2 font-semibold">Total (IDR)</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Transportation</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right font-mono font-semibold text-gray-800" id="fc-transport-display">0.00</td>
                                <td class="text-right"><button type="button" onclick="openModal('transport')" class="text-gray-400 hover:text-indigo-600"><i class="fa-solid fa-gear text-xs"></i></button></td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Guide Fee</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right font-mono font-semibold text-gray-800" id="fc-guide-display">0.00</td>
                                <td class="text-right"><button type="button" onclick="openModal('guide')" class="text-gray-400 hover:text-indigo-600"><i class="fa-solid fa-gear text-xs"></i></button></td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Mineral Water</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right font-mono font-semibold text-gray-800" id="fc-mw-display">0.00</td>
                                <td class="text-right"><button type="button" onclick="openModal('mw')" class="text-gray-400 hover:text-indigo-600"><i class="fa-solid fa-gear text-xs"></i></button></td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Flower Garland</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right font-mono font-semibold text-gray-800" id="fc-fg-display">0.00</td>
                                <td class="text-right"><button type="button" onclick="openModal('fg')" class="text-gray-400 hover:text-indigo-600"><i class="fa-solid fa-gear text-xs"></i></button></td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Guide Allowance</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="fc_guide_allowance" id="fc-guide-allowance"
                                        value="{{ old('fc_guide_allowance', $package?->fixedCosts->firstWhere('cost_type','guide_allowance')?->amount ?? 0) }}"
                                        class="w-36 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono font-semibold" oninput="recalcFixed()">
                                </td>
                                <td></td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-700">Luggage Truck</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="fc_luggage_truck" id="fc-luggage-truck"
                                        value="{{ old('fc_luggage_truck', $package?->fixedCosts->firstWhere('cost_type','luggage_truck')?->amount ?? 0) }}"
                                        class="w-36 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono font-semibold" oninput="recalcFixed()">
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-900">
                                <td class="py-2.5 pl-1 font-black text-white text-xs uppercase" colspan="2">Total Fixed Cost</td>
                                <td class="py-2.5 text-right font-black text-white font-mono text-sm" id="fc-grand-total">0.00</td>
                                <td></td>
                            </tr>
                            <tr class="bg-yellow-50 border-b-2 border-yellow-300">
                                <td class="py-2 pl-1 text-xs font-bold text-yellow-700" colspan="2">Per Pax</td>
                                <td class="py-2 text-right font-black text-yellow-800 font-mono text-sm" id="fc-grand-per-pax">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Hidden fields --}}
                    @php
                        // Transport: rows biasa vs manual
                        $transportRows = $package ? $package->fixedCosts->where('cost_type','transport') : collect();
                        $firstTransportMeta = $transportRows->first() ? json_decode($transportRows->first()->meta, true) : [];
                        $isTransportManual  = !empty($firstTransportMeta['manual']);
                        if ($isTransportManual) {
                            $savedTransportJson   = '[]';
                            $savedTransportManual = $transportRows->first()->amount ?? 0;
                        } else {
                            $savedTransportJson   = $transportRows->map(fn($r) => json_decode($r->meta, true))->filter()->values()->toJson();
                            $savedTransportManual = 0;
                        }
                        // Guide: rows biasa vs manual
                        $guideRows = $package ? $package->fixedCosts->where('cost_type','guide') : collect();
                        $firstGuideMeta = $guideRows->first() ? json_decode($guideRows->first()->meta, true) : [];
                        $isGuideManual  = !empty($firstGuideMeta['manual']);
                        if ($isGuideManual) {
                            $savedGuideJson   = '[]';
                            $savedGuideManual = $guideRows->first()->amount ?? 0;
                        } else {
                            $savedGuideJson   = $guideRows->map(fn($r) => json_decode($r->meta, true))->filter()->values()->toJson();
                            $savedGuideManual = 0;
                        }
                        $savedMwAmount = $package ? ($package->fixedCosts->firstWhere('cost_type','mineral_water')?->amount ?? 0) : 0;
                        $savedFgAmount = $package ? ($package->fixedCosts->firstWhere('cost_type','flower_garland')?->amount ?? 0) : 0;
                    @endphp
                    <input type="hidden" name="fc_transport_json" id="fc-transport-json" value="{{ old('fc_transport_json', $savedTransportJson) }}">
                    <input type="hidden" name="fc_transport_manual" id="fc-transport-manual" value="{{ old('fc_transport_manual', $savedTransportManual ?: '') }}">
                    <input type="hidden" name="fc_guide_json" id="fc-guide-json" value="{{ old('fc_guide_json', $savedGuideJson) }}">
                    <input type="hidden" name="fc_guide_manual" id="fc-guide-manual" value="{{ old('fc_guide_manual', $savedGuideManual ?: '') }}">
                    <input type="hidden" name="fc_mw_amount" id="fc-mw-amount" value="{{ old('fc_mw_amount', $savedMwAmount) }}">
                    <input type="hidden" name="fc_fg_amount" id="fc-fg-amount" value="{{ old('fc_fg_amount', $savedFgAmount) }}">
                    <input type="hidden" name="mw_price_per_dus" id="mw-price-per-dus" value="{{ old('mw_price_per_dus', $package?->mw_price_per_dus ?? 30000) }}">
                    <input type="hidden" name="mw_bottles_per_day" id="mw-bottles-per-day" value="{{ old('mw_bottles_per_day', $package?->mw_bottles_per_day ?? 2) }}">
                    <input type="hidden" name="fg_garland_price" id="fg-garland-price" value="{{ old('fg_garland_price', $package?->fg_garland_price ?? 50000) }}">
                    <input type="hidden" name="fg_flower_girl_price" id="fg-flower-girl-price" value="{{ old('fg_flower_girl_price', $package?->fg_flower_girl_price ?? 150000) }}">
                </div>

                {{-- RIGHT: Currency + Rates + Calculator --}}
                <div class="pl-5">
                    <table class="w-full text-sm mb-4">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                                <th class="text-left py-2 font-semibold">Config</th>
                                <th class="w-5"></th>
                                <th class="text-right py-2 font-semibold">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 font-semibold text-gray-700">Currency</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <select name="currency" id="f-currency" class="border border-gray-200 rounded px-2 py-1 text-xs font-semibold" onchange="recalc()">
                                        @foreach(['IDR','USD','MYR','SGD','EUR','AUD'] as $c)
                                            <option value="{{ $c }}" {{ old('currency',$package?->currency??'IDR')===$c?'selected':'' }}>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 font-semibold text-gray-700">Margin</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="margin" id="f-margin"
                                        value="{{ old('margin', $package?->margin ?? 0.85) }}"
                                        class="w-24 border border-gray-200 rounded px-2 py-1 text-xs text-right font-semibold" step="0.01" min="0.01" max="1" oninput="recalc()">
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-600">USD/IDR</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="rate_usd" id="f-rate-usd"
                                        value="{{ old('rate_usd', $package?->rate_usd ?? 16000) }}"
                                        class="w-28 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono" oninput="recalc()">
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-600">USD/MYR</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="rate_myr" id="f-rate-myr"
                                        value="{{ old('rate_myr', $package?->rate_myr ?? 4.70) }}"
                                        step="0.0001" class="w-28 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono" oninput="recalc()">
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 text-gray-600">USD/SGD</td>
                                <td class="text-gray-300 text-xs">:</td>
                                <td class="py-2 text-right">
                                    <input type="number" name="rate_sgd" id="f-rate-sgd"
                                        value="{{ old('rate_sgd', $package?->rate_sgd ?? 1.35) }}"
                                        step="0.0001" class="w-28 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono" oninput="recalc()">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Ambil Kurs button --}}
                    <button type="button" onclick="fetchRates()"
                        class="w-full text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-2 rounded flex items-center justify-center gap-2 mb-4">
                        <i class="fa-solid fa-rotate text-xs" id="rate-spin"></i> Ambil Kurs Pasar
                    </button>

                    {{-- Rate Calculator --}}
                    <div class="bg-gray-900 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            <i class="fa-solid fa-calculator mr-1 text-indigo-400"></i> Rate Calculator
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="number" id="rc-a" placeholder="0"
                                class="flex-1 bg-gray-800 border border-gray-600 text-white rounded px-2 py-1.5 text-xs font-mono text-right focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                oninput="doRateCalc()">
                            <select id="rc-op" class="bg-gray-800 border border-gray-600 text-white rounded px-2 py-1.5 text-xs font-bold focus:outline-none" onchange="doRateCalc()">
                                <option value="/">÷</option>
                                <option value="*">×</option>
                                <option value="+">+</option>
                                <option value="-">−</option>
                            </select>
                            <input type="number" id="rc-b" placeholder="0"
                                class="flex-1 bg-gray-800 border border-gray-600 text-white rounded px-2 py-1.5 text-xs font-mono text-right focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                oninput="doRateCalc()">
                            <span class="text-gray-500 font-bold">=</span>
                            <div id="rc-result" class="flex-1 bg-gray-800 border border-indigo-600 text-indigo-300 font-black text-sm rounded px-2 py-1.5 text-center font-mono">—</div>
                        </div>
                        <div class="flex gap-1.5 mt-2" id="rc-apply-btns" style="display:none!important">
                            <button type="button" onclick="applyRateCalc('usd')" class="text-xs bg-indigo-800 hover:bg-indigo-600 text-indigo-200 font-bold px-2 py-1 rounded">→ USD/IDR</button>
                            <button type="button" onclick="applyRateCalc('sgd')" class="text-xs bg-indigo-800 hover:bg-indigo-600 text-indigo-200 font-bold px-2 py-1 rounded">→ USD/SGD</button>
                            <button type="button" onclick="applyRateCalc('myr')" class="text-xs bg-indigo-800 hover:bg-indigo-600 text-indigo-200 font-bold px-2 py-1 rounded">→ USD/MYR</button>
                        </div>
                    </div>

                    {{-- Hidden EUR compat --}}
                    <input type="hidden" name="rate_eur" value="{{ old('rate_eur', $package?->rate_eur ?? 17000) }}">
                </div>
            </div>
        </div>

        {{-- VARIABLE COST + ITINERARY (below fixed cost grid, full width) --}}
        <div id="tab-la-bottom" class="bg-white border-x border-b border-gray-200 rounded-b-lg" style="display:none">
            <div class="px-5 pt-0 pb-4">
                <div class="border-t-2 border-dashed border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                            <i class="fa-solid fa-route mr-1 text-indigo-400"></i> Variable Cost — Itinerary
                            <span class="text-gray-400 font-normal normal-case ml-1">(harga per pax)</span>
                        </span>
                        <span class="text-xs text-gray-400">Durasi: <strong id="dur-label">—</strong></span>
                    </div>

                    {{-- 2-column layout: left=itinerary, right=kalkulasi --}}
                    <div class="grid grid-cols-5 gap-5">

                        {{-- LEFT: Day-by-day itinerary (3/5) --}}
                        <div class="col-span-3">
                            <div id="itin-container"></div>
                            {{-- Itinerary hidden inputs container --}}
                            <div id="itin-hidden-inputs"></div>
                        </div>

                        {{-- RIGHT: LA Cost Summary --}}
                        <div class="col-span-2">
                            <div class="sticky top-4">
                                <div class="bg-gray-900 rounded-lg p-4 text-white">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-4">
                                        <i class="fa-solid fa-calculator mr-1 text-indigo-400"></i> LA Cost Summary
                                    </p>

                                    {{-- Total IDR --}}
                                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                        <span class="text-sm text-gray-400">Fixed Cost / Pax (IDR)</span>
                                        <span class="font-mono font-semibold text-gray-200" id="sum-fixed-total">0</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                        <span class="text-sm text-gray-400">Variable Cost / Pax (IDR)</span>
                                        <span class="font-mono font-semibold text-gray-200" id="sum-variable-total">0</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-600 mb-3">
                                        <span class="text-sm font-bold text-white">Total LA / Pax (IDR)</span>
                                        <span class="font-mono font-black text-white text-base" id="sum-total-idr">IDR 0</span>
                                    </div>

                                    {{-- USD conversion info --}}
                                    <div class="flex justify-between items-center py-1.5 mb-1">
                                        <span class="text-xs text-gray-500">÷ Rate USD/IDR</span>
                                        <span class="font-mono text-xs text-gray-400" id="sum-rate-display2">—</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-700 mb-3">
                                        <span class="text-xs text-gray-500">÷ Margin</span>
                                        <span class="font-mono text-xs text-gray-400" id="sum-margin-display2">—</span>
                                    </div>

                                    {{-- Result --}}
                                    <div class="bg-indigo-900 rounded-lg p-3">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-bold text-indigo-300 uppercase tracking-wide">LA / Pax (USD)</span>
                                            <span class="font-mono font-bold text-indigo-200" id="sum-total-usd">USD 0</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-bold text-yellow-300 uppercase tracking-wide">LA with Margin (USD)</span>
                                            <span class="font-mono font-black text-xl text-yellow-300" id="sum-la-with-margin">USD 0</span>
                                        </div>
                                        <p class="text-xs text-indigo-500 mt-2">= Total IDR ÷ Rate ÷ Margin</p>
                                    </div>
                                </div>

                                {{-- Hidden ids still needed for JS compat --}}
                                <span id="sum-transport" class="hidden"></span>
                                <span id="sum-guide"     class="hidden"></span>
                                <span id="sum-mw"        class="hidden"></span>
                                <span id="sum-fg"        class="hidden"></span>
                                <span id="sum-ga"        class="hidden"></span>
                                <span id="sum-lt"        class="hidden"></span>
                                <span id="sum-itin-breakdown" class="hidden"></span>
                                <span id="sum-rate-display"   class="hidden"></span>
                                <span id="sum-margin-display" class="hidden"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Transport --}}
        <div id="modal-transport" class="fc-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center">
            <div style="background:white;border-radius:12px;width:680px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.3)">
                <div style="background:#1f2937;padding:16px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
                    <span style="color:white;font-weight:800;font-size:14px">① Transportation</span>
                    <button onclick="closeModal('transport')" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:20px;cursor:pointer">×</button>
                </div>
                {{-- Vendor + Vehicle selector header --}}
                <div style="padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e8ecf0;display:flex;gap:10px;align-items:center;flex-shrink:0">
                    <div style="flex:1">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;display:block;margin-bottom:4px">Vendor</label>
                        <select id="tr-vendor-select" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm" onchange="onTransportVendorChange(this)">
                            <option value="">— Pilih Vendor —</option>
                        </select>
                    </div>
                    <div style="flex:1">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;display:block;margin-bottom:4px">Vehicle</label>
                        <select id="tr-vehicle-select" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm" onchange="onTransportVehicleChange(this)" disabled>
                            <option value="">— Pilih Vehicle —</option>
                        </select>
                    </div>
                    <div style="flex-shrink:0;padding-top:16px">
                        <button type="button" onclick="addTransportRouteRow()" id="tr-add-route-btn" disabled
                            class="text-xs bg-gray-800 hover:bg-gray-700 text-white font-bold px-3 py-1.5 rounded disabled:opacity-40 disabled:cursor-not-allowed">
                            + Add Route
                        </button>
                    </div>
                </div>
                {{-- Routes table --}}
                <div style="padding:16px 20px;overflow:auto;flex:1">
                    <table class="w-full text-sm mb-2">
                        <thead><tr class="text-xs text-gray-400 uppercase border-b border-gray-200">
                            <th class="text-left py-1.5">Route / Program</th>
                            <th class="text-right py-1.5 w-32">Price</th>
                            <th class="text-center py-1.5 w-16">Qty</th>
                            <th class="text-right py-1.5 w-28">Subtotal</th>
                            <th class="w-7"></th>
                        </tr></thead>
                        <tbody id="transport-tbody"></tbody>
                        <tfoot><tr class="border-t-2 border-gray-200 bg-gray-50">
                            <td colspan="2" class="py-2 text-xs font-bold text-gray-500 uppercase">Total</td>
                            <td colspan="2" class="py-2 text-right font-black text-gray-800" id="transport-total">IDR 0</td>
                            <td></td>
                        </tr></tfoot>
                    </table>
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                        <span class="text-xs text-gray-400">atau isi manual total:</span>
                        <input type="number" id="fc-transport-manual-input" placeholder="0"
                            class="w-40 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono"
                            oninput="syncTransportManual(this.value)">
                    </div>
                </div>
                {{-- Footer with Save --}}
                <div style="padding:12px 20px;border-top:1px solid #e8ecf0;display:flex;justify-content:flex-end;gap:8px;flex-shrink:0;background:#f8fafc;border-radius:0 0 12px 12px">
                    <button type="button" onclick="closeModal('transport')" class="text-xs text-gray-500 hover:text-gray-700 font-semibold px-4 py-2 rounded border border-gray-200 bg-white">Batal</button>
                    <button type="button" onclick="saveTransportModal()" class="text-xs bg-blue-700 hover:bg-blue-800 text-white font-bold px-5 py-2 rounded">
                        <i class="fa-solid fa-check mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL: Guide --}}
        <div id="modal-guide" class="fc-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center">
            <div style="background:white;border-radius:12px;width:600px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.3)">
                <div style="background:#1f2937;padding:16px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
                    <span style="color:white;font-weight:800;font-size:14px">② Guide Fee</span>
                    <button onclick="closeModal('guide')" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:20px;cursor:pointer">×</button>
                </div>
                {{-- Language selector header --}}
                <div style="padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e8ecf0;display:flex;gap:10px;align-items:center;flex-shrink:0">
                    <div style="flex:1">
                        <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;display:block;margin-bottom:4px">Language</label>
                        <select id="gd-lang-select" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm" onchange="onGuideLangChange(this)">
                            <option value="">— Pilih Language —</option>
                        </select>
                    </div>
                    <div style="flex-shrink:0;padding-top:16px">
                        <button type="button" onclick="addGuideServiceRow()" id="gd-add-service-btn" disabled
                            class="text-xs bg-gray-800 hover:bg-gray-700 text-white font-bold px-3 py-1.5 rounded disabled:opacity-40 disabled:cursor-not-allowed">
                            + Add Service
                        </button>
                    </div>
                </div>
                {{-- Services table --}}
                <div style="padding:16px 20px;overflow:auto;flex:1">
                    <table class="w-full text-sm mb-2">
                        <thead><tr class="text-xs text-gray-400 uppercase border-b border-gray-200">
                            <th class="text-left py-1.5">Service</th>
                            <th class="text-center py-1.5 w-16">Qty</th>
                            <th class="text-right py-1.5 w-28">Rate</th>
                            <th class="text-right py-1.5 w-28">Subtotal</th>
                            <th class="w-7"></th>
                        </tr></thead>
                        <tbody id="guide-tbody"></tbody>
                        <tfoot><tr class="border-t-2 border-gray-200 bg-gray-50">
                            <td colspan="2" class="py-2 text-xs font-bold text-gray-500 uppercase">Total</td>
                            <td colspan="2" class="py-2 text-right font-black text-gray-800" id="guide-total">IDR 0</td>
                            <td></td>
                        </tr></tfoot>
                    </table>
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                        <span class="text-xs text-gray-400">atau isi manual total:</span>
                        <input type="number" id="fc-guide-manual-input" placeholder="0"
                            class="w-40 border border-gray-200 rounded px-2 py-1 text-xs text-right font-mono"
                            oninput="syncGuideManual(this.value)">
                    </div>
                </div>
                {{-- Footer with Save --}}
                <div style="padding:12px 20px;border-top:1px solid #e8ecf0;display:flex;justify-content:flex-end;gap:8px;flex-shrink:0;background:#f8fafc;border-radius:0 0 12px 12px">
                    <button type="button" onclick="closeModal('guide')" class="text-xs text-gray-500 hover:text-gray-700 font-semibold px-4 py-2 rounded border border-gray-200 bg-white">Batal</button>
                    <button type="button" onclick="saveGuideModal()" class="text-xs bg-blue-700 hover:bg-blue-800 text-white font-bold px-5 py-2 rounded">
                        <i class="fa-solid fa-check mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL: Mineral Water --}}
        <div id="modal-mw" class="fc-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center">
            <div style="background:white;border-radius:12px;width:420px;box-shadow:0 25px 60px rgba(0,0,0,.3)">
                <div style="background:#1f2937;padding:16px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center">
                    <span style="color:white;font-weight:800;font-size:14px">③ Mineral Water Config</span>
                    <button onclick="closeModal('mw')" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:20px;cursor:pointer">×</button>
                </div>
                <div style="padding:20px">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Harga per Dus (IDR)</label>
                            <input type="number" id="mw-price-per-dus-input"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" oninput="syncMw()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Botol per Orang per Hari</label>
                            <input type="number" id="mw-bottles-per-day-input"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" oninput="syncMw()">
                        </div>
                    </div>
                    <div class="bg-indigo-50 rounded p-3 text-xs text-indigo-700">
                        Formula: (min_pax × days × botol/hari ÷ 24) × harga/dus
                    </div>
                    <div class="mt-3 text-right font-black text-gray-800 text-base" id="mw-preview">IDR 0</div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('mw')" class="text-xs text-gray-500 font-semibold px-3 py-1.5 rounded border border-gray-200">Batal</button>
                        <button type="button" onclick="saveMwModal()" class="text-xs bg-blue-700 hover:bg-blue-800 text-white font-bold px-4 py-1.5 rounded"><i class="fa-solid fa-check mr-1"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Flower Garland --}}
        <div id="modal-fg" class="fc-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center">
            <div style="background:white;border-radius:12px;width:420px;box-shadow:0 25px 60px rgba(0,0,0,.3)">
                <div style="background:#1f2937;padding:16px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center">
                    <span style="color:white;font-weight:800;font-size:14px">④ Flower Garland Config</span>
                    <button onclick="closeModal('fg')" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:20px;cursor:pointer">×</button>
                </div>
                <div style="padding:20px">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Harga Garland per pax (IDR)</label>
                            <input type="number" id="fg-garland-price-input"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" oninput="syncFg()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Harga Gadis Bunga (IDR)</label>
                            <input type="number" id="fg-flower-girl-input"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm" oninput="syncFg()">
                        </div>
                    </div>
                    <div class="bg-indigo-50 rounded p-3 text-xs text-indigo-700">
                        Formula: (min_pax × harga garland) + harga gadis bunga
                    </div>
                    <div class="mt-3 text-right font-black text-gray-800 text-base" id="fg-preview">IDR 0</div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('fg')" class="text-xs text-gray-500 font-semibold px-3 py-1.5 rounded border border-gray-200">Batal</button>
                        <button type="button" onclick="saveFgModal()" class="text-xs bg-blue-700 hover:bg-blue-800 text-white font-bold px-4 py-1.5 rounded"><i class="fa-solid fa-check mr-1"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: Hotel --}}
        <div id="tab-hotel" class="tab-pane hidden bg-white rounded-b-lg rounded-tr-lg border border-gray-200 p-6">

            {{-- GRID CONFIG: Nights + Margin Room only --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-5">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Grid Config</p>
                <div class="grid grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                            Nights <span class="text-indigo-400 font-normal normal-case">(auto dari periode)</span>
                        </label>
                        <input type="number" name="hotel_nights_config" id="h-nights"
                            value="{{ old('hotel_nights_config', $package?->roomConfig?->nights ?? 0) }}"
                            class="w-full border border-gray-200 bg-gray-100 rounded px-3 py-2 text-sm text-gray-500 font-bold" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Margin TWN (IDR, flat)</label>
                        <input type="number" name="margin_twn_flat" id="h-margin-twn"
                            value="{{ old('margin_twn_flat', $package?->roomConfig?->margin_twn_flat ?? 25000) }}"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" oninput="recalcHotel()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tour Leader</label>
                        <select name="with_tl" id="h-with-tl" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" onchange="recalcHotel()">
                            <option value="0" {{ old('with_tl', $package?->roomConfig?->with_tl ?? 0) == 0 ? 'selected' : '' }}>Tidak</option>
                            <option value="1" {{ old('with_tl', $package?->roomConfig?->with_tl ?? 0) == 1 ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- HOTEL OPTIONS --}}
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Hotel Options</p>
                <button type="button" onclick="addHotelRow()"
                    class="bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold px-3 py-1.5 rounded">
                    + Add Hotel Option
                </button>
            </div>

            <div id="hotels-container">
                @if($package && $package->hotels->count())
                @foreach($package->hotels as $h)
                @php
                    $hMeta = $h->meta ? json_decode($h->meta, true) : [];
                @endphp
                <div class="hotel-row border border-gray-200 rounded-lg mb-3 overflow-hidden"
                    data-contract-id="{{ $h->hotel_contract_id }}"
                    data-surcharge-nights="0" data-surcharge-rate="0">
                    {{-- Row header --}}
                    <div class="bg-gray-700 px-4 py-2 flex justify-between items-center">
                        <span class="text-xs font-bold text-white uppercase">Option {{ $loop->iteration }}</span>
                        <button type="button" onclick="removeHotelRow(this)" class="text-xs text-red-400 hover:text-red-200 font-semibold">Remove</button>
                    </div>
                    {{-- Main fields --}}
                    <div class="p-4 grid grid-cols-4 gap-3 bg-gray-50">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Hotel</label>
                            <div class="relative">
                                <input type="text" name="hotel_names[]" value="{{ $h->hotel_name }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm hotel-name-input cursor-pointer bg-white"
                                    placeholder="Klik untuk pilih hotel..." readonly
                                    onclick="openHotelPicker(this.closest('.hotel-row'))">
                                <input type="hidden" name="hotel_contract_ids[]" value="{{ $h->hotel_contract_id }}">
                            </div>
                            <div class="mt-1 hotel-surcharge-info text-xs text-orange-600 font-semibold hidden"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Room Type</label>
                            <select name="hotel_room_types[]" class="w-full border border-gray-300 rounded px-2 py-2 text-sm hotel-type-select" onchange="recalcHotel()">
                                <option value="{{ $h->room_type }}">{{ $h->room_type ?: '— pilih hotel dulu —' }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Harga/Malam (IDR)</label>
                            <input type="number" name="hotel_room_rates[]" value="{{ $h->room_rate }}"
                                class="w-full border border-gray-300 rounded px-2 py-2 text-sm hotel-rate-input" oninput="recalcHotel()">
                        </div>
                    </div>
                    {{-- Extra costs per hotel option --}}
                    <div class="px-4 pb-4 pt-0 grid grid-cols-4 gap-3 bg-gray-50 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Extra Bed (IDR/malam)</label>
                            <input type="number" name="hotel_extra_bed[]" value="{{ $hMeta['extra_bed'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-extra-bed" oninput="recalcHotel()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">HD. Meeting (IDR)</label>
                            <input type="number" name="hotel_hd_meeting[]" value="{{ $hMeta['hd_meeting'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-hd-meeting" oninput="recalcHotel()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">FD. Meeting (IDR)</label>
                            <input type="number" name="hotel_fd_meeting[]" value="{{ $hMeta['fd_meeting'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-fd-meeting" oninput="recalcHotel()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Dinner (IDR/pax)</label>
                            <input type="number" name="hotel_dinner[]" value="{{ $hMeta['dinner'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-dinner" oninput="recalcHotel()">
                        </div>
                    </div>
                    <input type="hidden" name="hotel_nights[]" value="{{ $h->nights }}">
                    <input type="hidden" name="hotel_surcharge_nights[]" value="{{ $h->surcharge_nights ?? 0 }}">
                    <input type="hidden" name="hotel_surcharge_rates[]" value="{{ $h->surcharge_rate ?? 0 }}">
                </div>
                @endforeach
                @endif
            </div>

            {{-- FINAL CALCULATION --}}
            <div id="hotel-calc-panel" class="mt-5 bg-gray-900 rounded-lg p-4 text-white">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">
                    <i class="fa-solid fa-table mr-1 text-indigo-400"></i> Final Tour Package Calculation
                </p>
                <div class="text-xs text-gray-500 mb-3" id="hotel-calc-config-info">—</div>
                <div id="hotel-calc-table"></div>
            </div>
        </div>

        {{-- MODAL: Hotel Picker --}}
        <div id="modal-hotel-picker" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center">
            <div style="background:white;border-radius:12px;width:620px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.3)">
                <div style="background:#1f2937;padding:14px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
                    <span style="color:white;font-weight:800;font-size:14px">Pilih Hotel</span>
                    <button onclick="closeHotelPicker()" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:22px;cursor:pointer;line-height:1">×</button>
                </div>
                <div style="padding:14px 20px;border-bottom:1px solid #e8ecf0;flex-shrink:0">
                    <input id="hotel-picker-search" type="text" placeholder="Cari nama hotel..."
                        oninput="renderHotelPickerList(this.value)"
                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;box-sizing:border-box">
                </div>
                <div id="hotel-picker-list" style="overflow-y:auto;flex:1;padding:8px 12px"></div>
            </div>
        </div>

{{-- TAB 4: Proposal --}}
        <div id="tab-calc" class="tab-pane hidden bg-white rounded-b-lg rounded-tr-lg border border-gray-200 p-6">

            {{-- Client Details --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-5">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">
                    <i class="fa-solid fa-user mr-1 text-indigo-400"></i> Client Details
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-32 flex-shrink-0">Nama Client</span>
                        <span class="font-semibold text-gray-800" id="prop-agent">—</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-32 flex-shrink-0">Destinasi</span>
                        <span class="font-semibold text-gray-800" id="prop-destination">—</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-32 flex-shrink-0">Period Stay</span>
                        <span class="font-semibold text-gray-800" id="prop-period">—</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-32 flex-shrink-0">Total Pax</span>
                        <span class="font-semibold text-gray-800" id="prop-pax">—</span>
                    </div>
                </div>
            </div>

            {{-- Package Title --}}
            <h3 class="text-base font-black text-gray-800 mb-2" id="prop-pkg-title">Package</h3>

            {{-- ACCORDION WRAPPER --}}
            <div id="proposal-accordion" class="space-y-2 mb-4">

                {{-- SECTION 1: Package Rate (auto-open) --}}
                <div class="accordion-section border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleAccordion('acc-rate')"
                        class="accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-left">
                        <span class="text-sm font-bold text-gray-700">
                            <i class="fa-solid fa-tag mr-2 text-indigo-400"></i> Package Rate
                        </span>
                        <i id="acc-rate-icon" class="fa-solid fa-chevron-down text-gray-400 text-xs" style="transform:rotate(180deg)"></i>
                    </button>
                    <div id="acc-rate" class="accordion-body px-4 py-4">

                        {{-- Hotel Price Table --}}
                        <div id="prop-hotel-table" class="overflow-x-auto mb-1">
                            <p class="text-xs text-gray-400 italic">Lengkapi tab Hotel terlebih dahulu.</p>
                        </div>
                        <p id="prop-rate-note" class="text-xs text-gray-500 italic mt-1 mb-4"></p>

                        {{-- Custom Price Tables --}}
                        <div id="custom-tables-wrapper" class="mb-3"></div>
                        <button type="button" onclick="addCustomTable()"
                            class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-semibold mb-5">
                            <i class="fa-solid fa-plus mr-1"></i> Add Price Table
                        </button>
                        <input type="hidden" name="prop_custom_tables" id="prop-custom-tables-hidden">

                        {{-- Divider --}}
                        <hr class="my-4 border-gray-200">

                        {{-- Inclusion & Exclusion --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                    <i class="fa-solid fa-circle-check mr-1 text-green-500"></i> Inclusion
                                </label>
                                <div id="editor-inclusion" style="height:200px;background:white;border-radius:0 0 6px 6px"></div>
                                <input type="hidden" name="prop_inclusion" id="prop-inclusion-hidden">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                    <i class="fa-solid fa-circle-xmark mr-1 text-red-400"></i> Exclusion
                                </label>
                                <div id="editor-exclusion" style="height:200px;background:white;border-radius:0 0 6px 6px"></div>
                                <input type="hidden" name="prop_exclusion" id="prop-exclusion-hidden">
                            </div>
                        </div>

                        {{-- Terms & Conditions --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                <i class="fa-solid fa-file-contract mr-1 text-indigo-400"></i> Terms &amp; Conditions
                            </label>
                            <div id="editor-tnc" style="height:200px;background:white;border-radius:0 0 6px 6px"></div>
                            <input type="hidden" name="prop_tnc" id="prop-tnc-hidden">
                        </div>

                    </div>
                </div>

                {{-- SECTION 2: Proposed Itinerary --}}
                <div class="accordion-section border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleAccordion('acc-itin')"
                        class="accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-left">
                        <span class="text-sm font-bold text-gray-700">
                            <i class="fa-solid fa-map-location-dot mr-2 text-blue-500"></i> Proposed Itinerary
                        </span>
                        <i id="acc-itin-icon" class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <div id="acc-itin" class="accordion-body hidden px-4 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-gray-500 italic">Data diambil dari tab LA Cost. Klik Generate untuk auto-isi brief aktivitas.</p>
                            <div class="flex items-center gap-2">
                                <input type="hidden" id="gemini-api-key" value="">
                                <span class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-semibold"><i class="fa-solid fa-key mr-1"></i>API Key: Server</span>
                                <button type="button" onclick="generateAllBriefs()"
                                    class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Generate All
                                </button>
                            </div>
                        </div>
                        <div id="prop-itin-table" class="mb-4"></div>
                        <input type="hidden" name="prop_itinerary" id="prop-itinerary-hidden">
                        <input type="hidden" name="prop_itin_briefs" id="prop-itin-briefs-hidden">
                    </div>
                </div>

                {{-- SECTION 3: Restaurant Menu --}}
                <div class="accordion-section border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleAccordion('acc-menu')"
                        class="accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-left">
                        <span class="text-sm font-bold text-gray-700">
                            <i class="fa-solid fa-utensils mr-2 text-orange-500"></i> Restaurant Menu
                        </span>
                        <i id="acc-menu-icon" class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <div id="acc-menu" class="accordion-body hidden px-4 py-4">
                        <p class="text-xs text-gray-500 italic mb-3">Data diambil dari tab LA Cost (item type Restaurant). Pilih menu untuk setiap restaurant.</p>
                        <div id="prop-menu-container"></div>
                        <input type="hidden" name="prop_menu" id="prop-menu-hidden">

                        {{-- T&C section --}}
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Terms &amp; Conditions</label>
                            <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside bg-gray-50 rounded p-3 border border-gray-200">
                                <li>Other dishes and/or beverages ordered outside from the above proposed menu will charge based on personal basis.</li>
                                <li>The above menu will subject to changes without prior notice from the restaurants.</li>
                                <li>In conditions restaurant on fully booking situation at the time required, Diorama Destination has the rights to swap the restaurant's day used or change it with other similar restaurant and/or similar meals budget.</li>
                            </ul>
                        </div>

                        {{-- Note section --}}
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Note</label>
                            <div id="editor-menu" style="height:120px;background:white;border-radius:0 0 6px 6px"></div>
                        </div>
                    </div>
                </div>

            </div>{{-- end accordion --}}

        </div>

{{-- Save button --}}
        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('tour-packages.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2.5 rounded text-sm font-semibold">Cancel</a>
            <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded text-sm font-semibold">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Save Package
            </button>
        </div>
    </form>
</div>

@php
    $jsEntrances   = $entranceTickets->map(fn($e) => ['id'=>$e->id,'name'=>$e->attraction_name,'price'=>$e->adult_price])->values();
    $jsActivities  = $activityContracts->map(fn($a) => ['id'=>$a->id,'name'=>$a->vendor_name])->values();
    $jsRestaurants = $restaurantContracts->map(fn($r) => ['id'=>$r->id,'name'=>$r->vendor_name])->values();
    $jsHotels      = $hotelContracts->map(fn($h) => ['id'=>$h->id,'name'=>$h->hotel_name,'destination'=>$h->destination??''])->values();
    $jsItin        = $package ? $package->itinerary->map(fn($i) => ['day'=>$i->day,'name'=>$i->item_name,'type'=>$i->item_type,'ref_id'=>$i->ref_id,'price'=>$i->price_per_pax])->values() : collect([]);
@endphp
<script>
const entrances      = @json($jsEntrances);
const activities     = @json($jsActivities);
const restaurants    = @json($jsRestaurants);
const hotelContracts = @json($jsHotels);
const existingItin   = @json($jsItin);

const fmt = n => new Intl.NumberFormat('id-ID').format(Math.round(Number(n)||0));

function calcDuration() {
    const from = document.getElementById('f-period-from').value;
    const to   = document.getElementById('f-period-to').value;
    const dur  = document.getElementById('f-duration');
    if (!from || !to) return;
    const d1 = new Date(from), d2 = new Date(to);
    if (d2 < d1) { dur.value = ''; return; }
    const days   = Math.round((d2 - d1) / 86400000) + 1;
    const nights = days - 1;
    dur.value = days + 'D' + nights + 'N';
    const td = document.getElementById('total-days');
    if (td) td.value = days;
    buildDays();
    recalcMw();
    syncHotelNights();
    renderCustomTables();
    // Re-check surcharge for all hotel rows when period changes
    document.querySelectorAll('.hotel-row').forEach(function(row) {
        var hid = row.dataset.contractId;
        if (hid) checkSurcharge(row, hid);
    });
}

function syncHotelNights() {
    var durStr = document.getElementById('f-duration')?.value || '';
    var m = durStr.match(/D(\d+)N/);
    var nights = m ? parseInt(m[1]) : 0;
    var el = document.getElementById('h-nights');
    if (el) el.value = nights;
    // Update all hotel_nights[] hidden fields
    document.querySelectorAll('input[name="hotel_nights[]"]').forEach(function(inp) { inp.value = nights; });
}

async function fetchRates() {
    const spin = document.getElementById('rate-spin');
    spin.classList.add('fa-spin');
    try {
        const res  = await fetch('https://open.er-api.com/v6/latest/USD');
        const data = await res.json();
        if (data.result !== 'success') throw new Error();
        const r = data.rates;

        // USD/IDR
        const usdIdr = Math.round(r.IDR || 16000);
        setRate('usd', usdIdr, true);

        // USD/SGD (stored as decimal, e.g. 1.35)
        const usdSgd = r.SGD ? parseFloat(r.SGD.toFixed(4)) : 1.35;
        setRate('sgd', usdSgd, false);

        // USD/MYR
        const usdMyr = r.MYR ? parseFloat(r.MYR.toFixed(4)) : 4.70;
        setRate('myr', usdMyr, false);

        calcCurrency();
        recalc();
    } catch(e) {
        alert('Gagal ambil kurs. Cek koneksi.');
    } finally {
        spin.classList.remove('fa-spin');
    }
}

function setRate(k, marketVal, isInt) {
    const input = document.getElementById('f-rate-' + k);
    if (!input) return;
    const prev = parseFloat(input.value) || 0;
    const diff = marketVal - prev;
    const fmtVal = isInt
        ? new Intl.NumberFormat('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2}).format(marketVal)
        : marketVal.toFixed(2);
    const fmtDiff = isInt
        ? new Intl.NumberFormat('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2}).format(Math.abs(diff))
        : Math.abs(diff).toFixed(2);
    let ref = document.getElementById('rate-ref-' + k);
    if (!ref) {
        ref = document.createElement('div');
        ref.id = 'rate-ref-' + k;
        input.parentNode.appendChild(ref);
    }
    ref.textContent = 'Pasar: ' + fmtVal + (prev > 0 && diff !== 0 ? (diff > 0 ? ' +' : ' ') + fmtDiff : '');
    ref.style.cssText = 'font-size:10px;margin-top:2px;' + (diff > 0 ? 'color:#f97316' : diff < 0 ? 'color:#16a34a' : 'color:#9ca3af');
}

let rcalcVal = null;

function doRateCalc() {
    const a   = parseFloat(document.getElementById('rc-a')?.value);
    const b   = parseFloat(document.getElementById('rc-b')?.value);
    const op  = document.getElementById('rc-op')?.value || '/';
    const res = document.getElementById('rc-result');
    const btns = document.getElementById('rc-apply-btns');

    if (isNaN(a) || isNaN(b) || (op === '/' && b === 0)) {
        res.textContent = '—';
        res.style.color = '#818cf8';
        rcalcVal = null;
        if (btns) btns.style.setProperty('display','none','important');
        return;
    }

    let val;
    if      (op === '/') val = a / b;
    else if (op === '*') val = a * b;
    else if (op === '+') val = a + b;
    else if (op === '-') val = a - b;

    rcalcVal = val;
    const display = val >= 100 ? fmt(val) : parseFloat(val.toFixed(6)).toString();
    res.textContent = display;
    res.style.color = '#a5b4fc';
    if (btns) btns.style.removeProperty('display');
}

function applyRateCalc(field) {
    if (rcalcVal === null) return;
    const input = document.getElementById('f-rate-' + field);
    if (!input) return;
    input.value = rcalcVal >= 100 ? Math.round(rcalcVal) : parseFloat(rcalcVal.toFixed(6));
    input.dispatchEvent(new Event('input'));
    input.style.borderColor = '#6366f1';
    input.style.background = '#eef2ff';
    setTimeout(function() { input.style.borderColor = ''; input.style.background = ''; }, 1500);
}

function calcCurrency() { /* kept for compatibility */ }

function setTab(t) {
    window._activeTab = t;
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white','text-gray-800');
        b.classList.add('text-gray-400');
    });
    // LA tab has both the pane and the bottom section
    const pane = document.getElementById('tab-' + t);
    if (pane) pane.classList.remove('hidden');
    const bottom = document.getElementById('tab-la-bottom');
    if (bottom) bottom.style.display = (t === 'la') ? 'block' : 'none';
    const btn = document.getElementById('tab-btn-' + t);
    if (btn) { btn.classList.add('bg-white','text-gray-800'); btn.classList.remove('text-gray-400'); }
    if (t === 'la') recalcVariable();
    if (t === 'hotel') { syncHotelNights(); setTimeout(recalcHotel, 50); }
    if (t === 'calc') { recalc(); recalcProposal(); buildDays(); setTimeout(function(){ var b=document.getElementById('acc-rate'); if(b&&b.classList.contains('hidden')){b.classList.remove('hidden');var ic=document.getElementById('acc-rate-icon');if(ic)ic.style.transform='rotate(180deg)';setTimeout(initQuillEditors,50);} var bi=document.getElementById('acc-itin'); if(bi&&!bi.classList.contains('hidden')){renderItinTable();} },100); }
}


function recalc() {
    recalcLASummary();
    recalcProposal();
}

function recalcProposal() {
    // ── Client Details ──
    var agent       = document.getElementById('f-agent')?.value || '—';
    var destination = document.getElementById('f-destination')?.value || '—';
    var periodFrom  = document.getElementById('f-period-from')?.value || '';
    var periodTo    = document.getElementById('f-period-to')?.value || '';
    var pax         = document.getElementById('f-actual-pax')?.value || document.getElementById('f-pax')?.value || '—';
    var duration    = document.getElementById('f-duration')?.value || '';

    var periodStr = periodFrom && periodTo
        ? (new Date(periodFrom).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}))
          + ' – ' +
          (new Date(periodTo).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}))
        : '—';

    var gP = function(id) { return document.getElementById(id); };
    if (gP('prop-agent'))       gP('prop-agent').textContent       = agent;
    if (gP('prop-destination')) gP('prop-destination').textContent = destination;
    if (gP('prop-period'))      gP('prop-period').textContent      = periodStr;
    if (gP('prop-pax'))         gP('prop-pax').textContent         = pax + ' pax';

    // ── Package Title ──
    var pkgTitle = (duration || '—') + ' ' + (destination || '') + ' Package';
    if (gP('prop-pkg-title')) gP('prop-pkg-title').textContent = pkgTitle;

    // ── Hotel Table (mirror from hotel tab) ──
    var nights    = parseInt(document.getElementById('h-nights')?.value) || 0;
    var marginTWN = parseFloat(document.getElementById('h-margin-twn')?.value) || 0;
    var withTL    = parseInt(document.getElementById('h-with-tl')?.value) || 0;
    var rateUSD   = parseFloat(document.getElementById('f-rate-usd')?.value) || 16000;
    var rateMYR   = parseFloat(document.getElementById('f-rate-myr')?.value) || 4.70;
    var rateSGD   = parseFloat(document.getElementById('f-rate-sgd')?.value) || 1.35;
    var currency  = document.getElementById('f-currency')?.value || 'IDR';
    var margin    = parseFloat(document.getElementById('f-margin')?.value) || 0.85;
    var paxN      = Math.max(parseInt(document.getElementById('f-pax')?.value) || 1, 1);
    var laUSD     = (window._totalIDR || 0) / rateUSD / margin;

    var fxRate, curSym;
    if      (currency === 'MYR') { fxRate = rateMYR; curSym = 'MYR'; }
    else if (currency === 'SGD') { fxRate = rateSGD; curSym = 'SGD'; }
    else if (currency === 'USD') { fxRate = 1;       curSym = 'USD'; }
    else                         { fxRate = rateUSD; curSym = 'IDR'; }

    var hotelRows = document.querySelectorAll('.hotel-row');
    var tbl = gP('prop-hotel-table');
    if (!tbl) return;

    if (!hotelRows.length || nights === 0) {
        tbl.innerHTML = '<p class="text-xs text-gray-400 italic">Lengkapi tab Hotel terlebih dahulu.</p>';
    } else {
        // Check which extra columns have values
        var hasHD = false, hasFD = false, hasDinner = false;
        hotelRows.forEach(function(row) {
            if (parseFloat(row.querySelector('.hotel-hd-meeting')?.value) > 0) hasHD = true;
            if (parseFloat(row.querySelector('.hotel-fd-meeting')?.value) > 0) hasFD = true;
            if (parseFloat(row.querySelector('.hotel-dinner')?.value)     > 0) hasDinner = true;
        });

        // Price formatter: currency left, number right, fixed width using spans
        function fpCell(roomUSD, tlUSD) {
            var total = Math.round((roomUSD + laUSD + tlUSD) * fxRate);
            var sym = currency === 'IDR' ? 'IDR' : curSym;
            return '<span style="display:inline-flex;width:100%;justify-content:space-between;gap:8px">'
                 + '<span style="color:#9ca3af;font-weight:500">' + sym + '</span>'
                 + '<span>' + fmt(total) + '</span></span>';
        }
        function helperCell(usd) {
            var total = Math.round(usd * fxRate);
            var sym = currency === 'IDR' ? 'IDR' : curSym;
            return '<span style="display:inline-flex;width:100%;justify-content:space-between;gap:8px">'
                 + '<span style="color:#9ca3af;font-weight:500">' + sym + '</span>'
                 + '<span>' + fmt(total) + '</span></span>';
        }

        var thS  = 'padding:10px 16px;font-weight:700;font-size:11px;white-space:nowrap;letter-spacing:.05em;text-transform:uppercase;';
        var thSL = thS + 'text-align:left;';
        var thSR = thS + 'text-align:center;min-width:140px;border-left:1px solid #374151;';
        var html = '<table style="width:100%;border-collapse:collapse;font-size:13px;border:1px solid #d1d5db;border-radius:8px;overflow:hidden">'
            + '<thead><tr style="background:#1f2937;color:white">'
            + '<th style="' + thSL + '">Hotel</th>'
            + '<th style="' + thSL + '">Room Type</th>'
            + '<th style="' + thSR + '">SGL Occ.</th>'
            + '<th style="' + thSR + '">TWN / DBL</th>'
            + '<th style="' + thSR + '">Triple</th>';
        if (hasHD)     html += '<th style="' + thSR + '">HD Meeting</th>';
        if (hasFD)     html += '<th style="' + thSR + '">FD Meeting</th>';
        if (hasDinner) html += '<th style="' + thSR + '">Dinner</th>';
        html += '</tr></thead><tbody>';

        hotelRows.forEach(function(row, ri) {
            var hotelName = row.querySelector('.hotel-name-input')?.value || ('Option '+(ri+1));
            var roomType  = row.querySelector('.hotel-type-select')?.value || '';
            var roomRate  = parseFloat(row.querySelector('.hotel-rate-input')?.value) || 0;
            var extraBed  = parseFloat(row.querySelector('.hotel-extra-bed')?.value)  || 0;
            var hdMeeting = parseFloat(row.querySelector('.hotel-hd-meeting')?.value) || 0;
            var fdMeeting = parseFloat(row.querySelector('.hotel-fd-meeting')?.value) || 0;
            var dinner    = parseFloat(row.querySelector('.hotel-dinner')?.value)     || 0;
            var sNights   = parseInt(row.dataset.surchargeNights) || 0;
            var sRate     = parseFloat(row.dataset.surchargeRate) || 0;

            var roomCost      = roomRate * nights;
            var surchargeCost = sRate * sNights;
            var roomTotal     = roomCost + surchargeCost;
            var extraBedTotal = (roomRate + extraBed) * nights + surchargeCost;

            var sglIDR = roomTotal;
            var twnIDR = (roomTotal + marginTWN) / 2;
            var trpIDR = extraBedTotal / 3;

            var sglUSD = sglIDR / rateUSD;
            var twnUSD = twnIDR / rateUSD;
            var trpUSD = trpIDR / rateUSD;
            var tlUSD  = withTL ? (sglUSD / paxN) : 0;

            var hdUSD     = hdMeeting / paxN / rateUSD;
            var fdUSD     = fdMeeting / paxN / rateUSD;
            var dinnerUSD = dinner / rateUSD;

            var bg   = ri % 2 === 0 ? '#f9fafb' : '#ffffff';
            var tdName = 'padding:12px 16px;font-weight:700;color:#1f2937;border-bottom:1px solid #e5e7eb;';
            var tdType = 'padding:12px 16px;color:#6b7280;border-bottom:1px solid #e5e7eb;';
            var tdP    = 'padding:12px 16px;font-weight:800;min-width:140px;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;';
            html += '<tr style="background:' + bg + '">'
                + '<td style="' + tdName + '">' + hotelName + '</td>'
                + '<td style="' + tdType + '">' + (roomType||'—') + '</td>'
                + '<td style="' + tdP + 'color:#1d4ed8">' + fpCell(sglUSD, tlUSD) + '</td>'
                + '<td style="' + tdP + 'color:#065f46">' + fpCell(twnUSD, tlUSD) + '</td>'
                + '<td style="' + tdP + 'color:#1e40af">' + fpCell(trpUSD, tlUSD) + '</td>';
            if (hasHD)     html += '<td style="' + tdP + 'color:#7c3aed">' + (hdMeeting > 0 ? helperCell(hdUSD)     : '<span style="color:#d1d5db">—</span>') + '</td>';
            if (hasFD)     html += '<td style="' + tdP + 'color:#7c3aed">' + (fdMeeting > 0 ? helperCell(fdUSD)     : '<span style="color:#d1d5db">—</span>') + '</td>';
            if (hasDinner) html += '<td style="' + tdP + 'color:#7c3aed">' + (dinner    > 0 ? helperCell(dinnerUSD) : '<span style="color:#d1d5db">—</span>') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
        tbl.innerHTML = html;

        // Note below table
        var noteEl = document.getElementById('prop-rate-note');
        var curLabel = currency === 'IDR' ? 'IDR' : curSym;
        if (noteEl) noteEl.textContent = 'The above rates quoted in ' + curLabel + ', net per pax';
    }

    // ── Init Quill editors with default content (only if empty) ──
    var nightsLabel = nights > 0 ? nights + ' night' + (nights > 1 ? 's' : '') : 'x nights';
    if (window._quillInclusion && !window._quillInclusion.getText().trim()) {
        window._quillInclusion.setContents([
            {insert: nightsLabel + ' stay at above selected hotel\n'},
            {insert: 'Return transfer, airport - hotel - airport\n'},
            {insert: 'Touring and journey as per itinerary\n'},
            {insert: 'Meals and dining as per itinerary\n'},
            {insert: 'Entrance fee at all places visited as per itinerary\n'},
            {insert: 'English speaking guide assistance\n'},
        ]);
    }
    if (window._quillExclusion && !window._quillExclusion.getText().trim()) {
        window._quillExclusion.setContents([
            {insert: 'Air ticket / air fare\n'},
            {insert: 'Incidental bill at hotel\n'},
            {insert: 'Bali International Levy at IDR 150,000/pax, can be paid through ilovebali.go.id\n'},
            {insert: 'Other expenses not specified above and/or in the itinerary\n'},
        ]);
    }
}


// ─── MODAL OPEN/CLOSE ─────────────────────────────────────────
function openModal(type) {
    // Sync input values before opening
    if (type === 'mw') {
        document.getElementById('mw-price-per-dus-input').value  = document.getElementById('mw-price-per-dus').value;
        document.getElementById('mw-bottles-per-day-input').value = document.getElementById('mw-bottles-per-day').value;
        syncMwPreview();
    }
    if (type === 'fg') {
        document.getElementById('fg-garland-price-input').value  = document.getElementById('fg-garland-price').value;
        document.getElementById('fg-flower-girl-input').value    = document.getElementById('fg-flower-girl-price').value;
        syncFgPreview();
    }
    const m = document.getElementById('modal-' + type);
    if (m) { m.style.display = 'flex'; }
}

function closeModal(type) {
    const m = document.getElementById('modal-' + type);
    if (m) m.style.display = 'none';
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    document.querySelectorAll('.fc-modal').forEach(function(m) {
        if (e.target === m) m.style.display = 'none';
    });
});

// ─── MINERAL WATER ────────────────────────────────────────────
function syncMw() {
    // Sync modal inputs → hidden fields
    document.getElementById('mw-price-per-dus').value   = document.getElementById('mw-price-per-dus-input').value;
    document.getElementById('mw-bottles-per-day').value = document.getElementById('mw-bottles-per-day-input').value;
    syncMwPreview();
    recalcMw();
}

function syncMwPreview() {
    const pax      = parseInt(document.getElementById('f-pax')?.value)||0;
    const durStr   = document.getElementById('f-duration')?.value || '';
    const durMatch = durStr.match(/^(\d+)D/);
    const days     = durMatch ? parseInt(durMatch[1]) : 1;
    const bottles  = parseFloat(document.getElementById('mw-bottles-per-day-input')?.value)||0;
    const priceDus = parseFloat(document.getElementById('mw-price-per-dus-input')?.value)||0;
    const total    = ((pax * days * bottles) / 24) * priceDus;
    const prev = document.getElementById('mw-preview');
    if (prev) prev.textContent = 'IDR ' + fmt(total);
}

function toggleMwConfig() { openModal('mw'); }

function recalcMw() {
    const pax      = parseInt(document.getElementById('f-pax')?.value)||0;
    const durStr   = document.getElementById('f-duration')?.value || '';
    const durMatch = durStr.match(/^(\d+)D/);
    const days     = durMatch ? parseInt(durMatch[1]) : 1;
    const bottles  = parseFloat(document.getElementById('mw-bottles-per-day')?.value)||0;
    const priceDus = parseFloat(document.getElementById('mw-price-per-dus')?.value)||0;
    const total    = ((pax * days * bottles) / 24) * priceDus;
    document.getElementById('fc-mw-amount').value = total;
    recalcFixed();
}

// ─── FLOWER GARLAND ───────────────────────────────────────────
function syncFg() {
    document.getElementById('fg-garland-price').value      = document.getElementById('fg-garland-price-input').value;
    document.getElementById('fg-flower-girl-price').value  = document.getElementById('fg-flower-girl-input').value;
    syncFgPreview();
    recalcFg();
}

function syncFgPreview() {
    const pax          = parseInt(document.getElementById('f-pax')?.value)||0;
    const garlandPrice = parseFloat(document.getElementById('fg-garland-price-input')?.value)||0;
    const girlPrice    = parseFloat(document.getElementById('fg-flower-girl-input')?.value)||0;
    const total        = (pax * garlandPrice) + girlPrice;
    const prev = document.getElementById('fg-preview');
    if (prev) prev.textContent = 'IDR ' + fmt(total);
}

function toggleFgConfig() { openModal('fg'); }

function recalcFg() {
    const pax          = parseInt(document.getElementById('f-pax')?.value)||0;
    const garlandPrice = parseFloat(document.getElementById('fg-garland-price')?.value)||0;
    const girlPrice    = parseFloat(document.getElementById('fg-flower-girl-price')?.value)||0;
    const total        = (pax * garlandPrice) + girlPrice;
    document.getElementById('fc-fg-amount').value = total;
    recalcFixed();
}

// ─── TRANSPORT ────────────────────────────────────────────────
let transportContracts = [];
let currentTransportVehicleId = null;
let currentTransportVehicleRoutes = [];

async function initTransport() {
    try {
        const res = await fetch('/tour-packages-api/transport-contracts');
        transportContracts = await res.json();
        const vsel = document.getElementById('tr-vendor-select');
        if (vsel) {
            transportContracts.forEach(c => {
                const o = document.createElement('option');
                o.value = c.id; o.textContent = c.vendor_name;
                vsel.appendChild(o);
            });
        }
    } catch(e) {}
    // Load manual override
    const manualVal = parseFloat(document.getElementById('fc-transport-manual')?.value) || 0;
    if (manualVal > 0) {
        const inp = document.getElementById('fc-transport-manual-input');
        if (inp) inp.value = manualVal;
        syncTransportManual(manualVal);
        return;
    }
    // Load saved rows from JSON
    try {
        const saved = JSON.parse(document.getElementById('fc-transport-json')?.value || '[]');
        if (saved.length) {
            for (const r of saved) {
                // Pre-fetch vehicles and routes for this row
                if (r.vehicle_id) {
                    try {
                        const vRes = await fetch('/tour-packages-api/transport-rates?vehicle_id=' + r.vehicle_id);
                        currentTransportVehicleRoutes = await vRes.json();
                        currentTransportVehicleId = r.vehicle_id;
                    } catch(e) { currentTransportVehicleRoutes = []; }
                }
                addTransportRouteRow(r);
            }
            recalcFixed();
        }
    } catch(e) {}
}

async function onTransportVendorChange(sel) {
    const vehicleSel = document.getElementById('tr-vehicle-select');
    vehicleSel.innerHTML = '<option value="">Loading...</option>';
    vehicleSel.disabled = true;
    document.getElementById('tr-add-route-btn').disabled = true;
    currentTransportVehicleId = null;
    currentTransportVehicleRoutes = [];
    if (!sel.value) { vehicleSel.innerHTML = '<option value="">— Pilih Vehicle —</option>'; return; }
    const res = await fetch('/tour-packages-api/transport-vehicles?contract_id=' + sel.value);
    const vehicles = await res.json();
    vehicleSel.innerHTML = '<option value="">— Pilih Vehicle —</option>';
    vehicles.forEach(v => {
        const o = document.createElement('option');
        o.value = v.id;
        o.textContent = v.vehicle_name + ' (' + v.category + (v.capacity ? ', ' + v.capacity + ' seat' : '') + ')';
        vehicleSel.appendChild(o);
    });
    vehicleSel.disabled = false;
}

async function onTransportVehicleChange(sel) {
    document.getElementById('tr-add-route-btn').disabled = true;
    currentTransportVehicleId = null;
    currentTransportVehicleRoutes = [];
    if (!sel.value) return;
    const res = await fetch('/tour-packages-api/transport-rates?vehicle_id=' + sel.value);
    currentTransportVehicleRoutes = await res.json();
    currentTransportVehicleId = sel.value;
    document.getElementById('tr-add-route-btn').disabled = false;
}

function addTransportRouteRow(data) {
    const tbody = document.getElementById('transport-tbody');
    if (!tbody) return;
    const routes = currentTransportVehicleRoutes;
    const routeOpts = routes.map(rt =>
        '<option value="' + rt.id + '" data-price="' + rt.price + '"' +
        (rt.id === (data?.rate_id || '') ? ' selected' : '') + '>' +
        rt.route_name + ' (' + rt.route_type + ')</option>'
    ).join('');

    const tr = document.createElement('tr');
    tr.className = 'border-b border-gray-100 transport-row';
    tr.dataset.contractId = document.getElementById('tr-vendor-select')?.value || data?.contract_id || '';
    tr.dataset.vehicleId  = currentTransportVehicleId || data?.vehicle_id || '';
    tr.dataset.rateId     = data?.rate_id || '';

    const price = data?.price || (routes.length ? routes[0].price : 0);
    const qty   = data?.qty || 1;

    tr.innerHTML =
        '<td class="py-1.5 pr-2">' +
        (routeOpts
            ? '<select class="w-full border border-gray-200 rounded px-2 py-1 text-xs tr-route" onchange="setTransportPriceFromRow(this)">' +
              '<option value="">— Route —</option>' + routeOpts + '</select>'
            : '<input type="text" class="w-full border border-gray-200 rounded px-2 py-1 text-xs tr-route-name" value="' + (data?.route_name || '') + '" placeholder="Route name">') +
        '</td>' +
        '<td class="py-1.5 pr-2"><input type="number" class="w-full border border-gray-200 rounded px-2 py-1 text-xs text-right tr-price" value="' + price + '" oninput="recalcTransportRow(this)"></td>' +
        '<td class="py-1.5 pr-2 text-center"><input type="number" class="w-12 border border-gray-200 rounded px-1 py-1 text-xs text-center tr-qty" value="' + qty + '" min="1" oninput="recalcTransportRow(this)"></td>' +
        '<td class="py-1.5 pr-2 text-right font-bold text-xs text-gray-700 tr-subtotal">' + fmt(price * qty) + '</td>' +
        '<td class="py-1.5 text-center"><button type="button" onclick="this.closest(&quot;tr&quot;).remove();recalcTransportModal()" class="text-red-400 hover:text-red-600 text-sm">×</button></td>';

    tbody.appendChild(tr);
    recalcTransportModal();
}

function setTransportPriceFromRow(sel) {
    const tr = sel.closest("tr");
    tr.dataset.rateId = sel.value;
    const price = sel.options[sel.selectedIndex]?.dataset?.price || 0;
    const pi = tr.querySelector('.tr-price');
    pi.value = price;
    recalcTransportRow(pi);
}

function recalcTransportRow(el) {
    const tr = el.closest("tr");
    const price = parseFloat(tr.querySelector('.tr-price').value) || 0;
    const qty   = parseFloat(tr.querySelector('.tr-qty').value) || 1;
    tr.querySelector('.tr-subtotal').textContent = fmt(price * qty);
    recalcTransportModal();
}

function recalcTransportModal() {
    let total = 0;
    document.querySelectorAll('.transport-row').forEach(tr => {
        total += (parseFloat(tr.querySelector('.tr-price')?.value) || 0) *
                 (parseFloat(tr.querySelector('.tr-qty')?.value) || 1);
    });
    const el = document.getElementById('transport-total');
    if (el) el.textContent = 'IDR ' + fmt(total);
}

function saveTransportModal() {
    // Serialize rows → hidden field, update display
    const trows = [];
    document.querySelectorAll('.transport-row').forEach(tr => {
        trows.push({
            contract_id: tr.dataset.contractId,
            vehicle_id:  tr.dataset.vehicleId,
            rate_id:     tr.dataset.rateId,
            price:       parseFloat(tr.querySelector('.tr-price')?.value) || 0,
            qty:         parseInt(tr.querySelector('.tr-qty')?.value) || 1,
        });
    });
    const el = document.getElementById('fc-transport-json');
    if (el) el.value = JSON.stringify(trows);
    // Clear manual if rows exist
    if (trows.length) {
        const m = document.getElementById('fc-transport-manual');
        if (m) m.value = '';
        const mi = document.getElementById('fc-transport-manual-input');
        if (mi) mi.value = '';
    }
    recalcFixed();
    closeModal('transport');
}

// ─── GUIDE ─────────────────────────────────────────────────────
let guideLanguages = [];
let currentGuideLangId = null;
let currentGuideServices = [];

async function initGuide() {
    try {
        const res = await fetch('/tour-packages-api/guide-languages');
        guideLanguages = await res.json();
        const lsel = document.getElementById('gd-lang-select');
        if (lsel) {
            guideLanguages.forEach(l => {
                const o = document.createElement('option');
                o.value = l.id;
                o.textContent = l.language_name + (l.destination ? ' (' + l.destination + ')' : '');
                lsel.appendChild(o);
            });
        }
    } catch(e) {}
    // Load manual override
    const manualVal = parseFloat(document.getElementById('fc-guide-manual')?.value) || 0;
    if (manualVal > 0) {
        const inp = document.getElementById('fc-guide-manual-input');
        if (inp) inp.value = manualVal;
        syncGuideManual(manualVal);
        return;
    }
    // Load saved rows
    try {
        const saved = JSON.parse(document.getElementById('fc-guide-json')?.value || '[]');
        if (saved.length) {
            for (const r of saved) {
                await addGuideServiceRow(r);
            }
            recalcFixed();
        }
    } catch(e) {}
}

async function onGuideLangChange(sel) {
    document.getElementById('gd-add-service-btn').disabled = true;
    currentGuideLangId = null;
    currentGuideServices = [];
    if (!sel.value) return;
    const res = await fetch('/tour-packages-api/guide-services?language_id=' + sel.value);
    currentGuideServices = await res.json();
    currentGuideLangId = sel.value;
    document.getElementById('gd-add-service-btn').disabled = false;
}

async function addGuideServiceRow(data) {
    const tbody = document.getElementById('guide-tbody');
    if (!tbody) return;
    const pax = parseInt(document.getElementById('f-pax')?.value) || 1;
    let services = currentGuideServices;

    // If loading saved data with a different lang, fetch services
    if (data?.lang_id && data.lang_id !== currentGuideLangId) {
        try {
            const res = await fetch('/tour-packages-api/guide-services?language_id=' + data.lang_id);
            services = await res.json();
        } catch(e) { services = []; }
    }

    const serviceOpts = services.map(s =>
        '<option value="' + s.id + '"' + (s.id === (data?.service_id || '') ? ' selected' : '') + '>' +
        s.service_name + ' (' + s.service_type + ')</option>'
    ).join('');

    let rate = data?.rate || 0;
    // Auto-fetch rate if not provided
    if (!rate && data?.service_id) {
        try {
            const res = await fetch('/tour-packages-api/guide-rate?service_id=' + data.service_id + '&pax=' + pax);
            const d = await res.json();
            rate = d.rate || 0;
        } catch(e) {}
    }
    // Auto rate for first service if new row
    if (!rate && services.length && !data) {
        try {
            const res = await fetch('/tour-packages-api/guide-rate?service_id=' + services[0].id + '&pax=' + pax);
            const d = await res.json();
            rate = d.rate || 0;
        } catch(e) {}
    }

    const qty = data?.qty || 1;
    const tr = document.createElement('tr');
    tr.className = 'border-b border-gray-100 guide-row';
    tr.dataset.langId    = data?.lang_id || currentGuideLangId || '';
    tr.dataset.serviceId = data?.service_id || (services[0]?.id || '');

    tr.innerHTML =
        '<td class="py-1.5 pr-2">' +
        (serviceOpts
            ? '<select class="w-full border border-gray-200 rounded px-2 py-1 text-xs gd-service" onchange="loadGuideRateFromRow(this)">' +
              '<option value="">— Service —</option>' + serviceOpts + '</select>'
            : '<input type="text" class="w-full border border-gray-200 rounded px-2 py-1 text-xs" value="' + (data?.service_name || '') + '" placeholder="Service name">') +
        '</td>' +
        '<td class="py-1.5 pr-2 text-center"><input type="number" class="w-12 border border-gray-200 rounded px-1 py-1 text-xs text-center gd-qty" value="' + qty + '" min="1" oninput="recalcGuideRow(this)"></td>' +
        '<td class="py-1.5 pr-2"><input type="number" class="w-full border border-gray-200 rounded px-2 py-1 text-xs text-right gd-rate" value="' + rate + '" oninput="recalcGuideRow(this)"></td>' +
        '<td class="py-1.5 pr-2 text-right font-bold text-xs text-gray-700 gd-subtotal">' + fmt(rate * qty) + '</td>' +
        '<td class="py-1.5 text-center"><button type="button" onclick="this.closest(&quot;tr&quot;).remove();recalcGuideModal()" class="text-red-400 hover:text-red-600 text-sm">×</button></td>';

    tbody.appendChild(tr);
    recalcGuideModal();
}

async function loadGuideRateFromRow(sel) {
    const tr = sel.closest("tr");
    tr.dataset.serviceId = sel.value;
    if (!sel.value) return;
    const pax = parseInt(document.getElementById('f-pax')?.value) || 1;
    const res = await fetch('/tour-packages-api/guide-rate?service_id=' + sel.value + '&pax=' + pax);
    const data = await res.json();
    const ri = tr.querySelector('.gd-rate');
    ri.value = data.rate || 0;
    recalcGuideRow(ri);
}

function recalcGuideRow(el) {
    const tr = el.closest("tr");
    const rate = parseFloat(tr.querySelector('.gd-rate').value) || 0;
    const qty  = parseFloat(tr.querySelector('.gd-qty').value) || 1;
    tr.querySelector('.gd-subtotal').textContent = fmt(rate * qty);
    recalcGuideModal();
}

function recalcGuideModal() {
    let total = 0;
    document.querySelectorAll('.guide-row').forEach(tr => {
        total += (parseFloat(tr.querySelector('.gd-rate')?.value) || 0) *
                 (parseFloat(tr.querySelector('.gd-qty')?.value) || 1);
    });
    const el = document.getElementById('guide-total');
    if (el) el.textContent = 'IDR ' + fmt(total);
}

function saveGuideModal() {
    const grows = [];
    document.querySelectorAll('.guide-row').forEach(tr => {
        grows.push({
            lang_id:    tr.dataset.langId,
            service_id: tr.dataset.serviceId,
            rate:       parseFloat(tr.querySelector('.gd-rate')?.value) || 0,
            qty:        parseInt(tr.querySelector('.gd-qty')?.value) || 1,
        });
    });
    const el = document.getElementById('fc-guide-json');
    if (el) el.value = JSON.stringify(grows);
    if (grows.length) {
        const m = document.getElementById('fc-guide-manual');
        if (m) m.value = '';
        const mi = document.getElementById('fc-guide-manual-input');
        if (mi) mi.value = '';
    }
    recalcFixed();
    closeModal('guide');
}

// ─── MW & FG SAVE ──────────────────────────────────────────────
function saveMwModal() {
    syncMw();
    closeModal('mw');
}

function saveFgModal() {
    syncFg();
    closeModal('fg');
}

// ─── MANUAL OVERRIDES ─────────────────────────────────────────
function syncTransportManual(val) {
    document.getElementById('fc-transport-manual').value = val;
    // Override transport display with manual value
    if (val > 0) {
        const pax = Math.max(parseInt(document.getElementById('f-pax')?.value)||1, 1);
        document.getElementById('fc-transport-display').textContent = fmtMono(val);
        document.getElementById('fc-transport-pax') && (document.getElementById('fc-transport-pax').textContent = fmtMono(val/pax));
        // Clear transport rows so manual takes over
        document.querySelectorAll('.transport-row').forEach(r => r.remove());
        if (document.getElementById('transport-total')) document.getElementById('transport-total').textContent = 'IDR ' + fmt(val);
    }
    recalcFixed();
}

function syncGuideManual(val) {
    document.getElementById('fc-guide-manual').value = val;
    if (val > 0) {
        const pax = Math.max(parseInt(document.getElementById('f-pax')?.value)||1, 1);
        document.getElementById('fc-guide-display').textContent = fmtMono(val);
        document.getElementById('fc-guide-pax') && (document.getElementById('fc-guide-pax').textContent = fmtMono(val/pax));
        document.querySelectorAll('.guide-row').forEach(r => r.remove());
        if (document.getElementById('guide-total')) document.getElementById('guide-total').textContent = 'IDR ' + fmt(val);
    }
    recalcFixed();
}

// ─── GRAND TOTAL ──────────────────────────────────────────────
function gEl(id) { return document.getElementById(id); }

function fmtMono(n) { return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(Number(n)||0); }

function recalcFixed() {
    const pax = Math.max(parseInt(gEl('f-pax')?.value)||1, 1);

    // ── Transport ──
    let transportTotal = 0;
    const transportManual = parseFloat(gEl('fc-transport-manual')?.value) || 0;
    const transportDomRows = document.querySelectorAll('.transport-row');
    if (transportManual > 0) {
        transportTotal = transportManual;
    } else if (transportDomRows.length > 0) {
        transportDomRows.forEach(tr => {
            transportTotal += (parseFloat(tr.querySelector('.tr-price')?.value)||0) *
                              (parseFloat(tr.querySelector('.tr-qty')?.value)||1);
        });
    } else {
        // DOM not yet built (async init) — read from hidden JSON field
        try {
            const saved = JSON.parse(gEl('fc-transport-json')?.value || '[]');
            saved.forEach(r => { transportTotal += (r.price||0) * (r.qty||1); });
        } catch(e) {}
    }
    if (gEl('transport-total')) gEl('transport-total').textContent = 'IDR ' + fmt(transportTotal);

    // ── Guide ──
    let guideTotal = 0;
    const guideManual = parseFloat(gEl('fc-guide-manual')?.value) || 0;
    const guideDomRows = document.querySelectorAll('.guide-row');
    if (guideManual > 0) {
        guideTotal = guideManual;
    } else if (guideDomRows.length > 0) {
        guideDomRows.forEach(tr => {
            guideTotal += (parseFloat(tr.querySelector('.gd-rate')?.value)||0) *
                          (parseFloat(tr.querySelector('.gd-qty')?.value)||1);
        });
    } else {
        try {
            const saved = JSON.parse(gEl('fc-guide-json')?.value || '[]');
            saved.forEach(r => { guideTotal += (r.rate||0) * (r.qty||1); });
        } catch(e) {}
    }
    if (gEl('guide-total')) gEl('guide-total').textContent = 'IDR ' + fmt(guideTotal);

    const mwTotal = parseFloat(gEl('fc-mw-amount')?.value) || 0;
    const fgTotal = parseFloat(gEl('fc-fg-amount')?.value) || 0;
    const gaTotal = parseFloat(gEl('fc-guide-allowance')?.value) || 0;
    const ltTotal = parseFloat(gEl('fc-luggage-truck')?.value) || 0;

    const grandTotal = transportTotal + guideTotal + mwTotal + fgTotal + gaTotal + ltTotal;

    if (gEl('fc-transport-display')) gEl('fc-transport-display').textContent = fmtMono(transportTotal);
    if (gEl('fc-guide-display'))     gEl('fc-guide-display').textContent     = fmtMono(guideTotal);
    if (gEl('fc-mw-display'))        gEl('fc-mw-display').textContent        = fmtMono(mwTotal);
    if (gEl('fc-fg-display'))        gEl('fc-fg-display').textContent        = fmtMono(fgTotal);
    if (gEl('fc-grand-total'))       gEl('fc-grand-total').textContent       = fmtMono(grandTotal);
    if (gEl('fc-grand-per-pax'))     gEl('fc-grand-per-pax').textContent     = fmtMono(grandTotal/pax);
    if (gEl('fc-grand-per-pax-2'))   gEl('fc-grand-per-pax-2').textContent   = fmtMono(grandTotal/pax);

    // Serialize hanya kalau DOM rows sudah ada (jangan overwrite saat init)
    if (transportDomRows.length > 0) {
        const trows = [];
        transportDomRows.forEach(tr => {
            trows.push({
                contract_id: tr.dataset.contractId,
                vehicle_id:  tr.dataset.vehicleId,
                rate_id:     tr.dataset.rateId,
                price:       parseFloat(tr.querySelector('.tr-price')?.value) || 0,
                qty:         parseInt(tr.querySelector('.tr-qty')?.value) || 1,
            });
        });
        if (gEl('fc-transport-json')) gEl('fc-transport-json').value = JSON.stringify(trows);
    }
    if (guideDomRows.length > 0) {
        const grows = [];
        guideDomRows.forEach(tr => {
            grows.push({
                lang_id:    tr.dataset.langId,
                service_id: tr.dataset.serviceId,
                rate:       parseFloat(tr.querySelector('.gd-rate')?.value) || 0,
                qty:        parseInt(tr.querySelector('.gd-qty')?.value) || 1,
            });
        });
        if (gEl('fc-guide-json')) gEl('fc-guide-json').value = JSON.stringify(grows);
    }

    window.fcGrandPerPax = grandTotal / pax;
    recalcVariable();
    recalc();
}

function addLaRow() {
    const tbody = document.getElementById('la-tbody');
    const tr = document.createElement('tr');
    tr.className = 'la-row border-b border-gray-100';
    tr.innerHTML =
        '<td class="py-2 pr-3"><input type="text" name="la_names[]" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm" placeholder="Item name"></td>' +
        '<td class="py-2 pl-3"><input type="number" name="la_amounts[]" value="0" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm text-right la-amount" oninput="recalc()"></td>' +
        '<td class="py-2 pl-3 text-right font-semibold text-green-600 la-per-pax text-sm">—</td>' +
        '<td class="py-2 pl-2 text-center"><button type="button" onclick="this.closest(\'tr\').remove();recalc()" class="text-red-400 hover:text-red-600 text-lg">x</button></td>';
    tbody.appendChild(tr);
}

let itinData = {};

function buildDays() {
    const durStr = document.getElementById('f-duration')?.value || '';
    const durMatch = durStr.match(/^(\d+)D/);
    const n = durMatch ? parseInt(durMatch[1]) : (parseInt(document.getElementById('total-days')?.value) || 0);

    // Update dur-label
    const dl = document.getElementById('dur-label');
    if (dl) dl.textContent = durStr || '—';

    // Update total-days hidden
    const td = document.getElementById('total-days');
    if (td) td.value = n;

    if (!n) {
        const container = document.getElementById('itin-container');
        if (container) container.innerHTML = '<p style="color:#adb5bd;font-size:12px;text-align:center;padding:20px">Isi periode di tab Package Info dulu</p>';
        return;
    }

    // Harvest current DOM state before rebuild — reset per day first to avoid duplicates
    var harvested = {};
    document.querySelectorAll('.itin-row').forEach(function(row) {
        var day = parseInt(row.dataset.day);
        if (!harvested[day]) harvested[day] = [];
        var name  = row.querySelector('.itin-name')?.value || '';
        var type  = row.querySelector('.itin-type')?.value || 'manual';
        var price = parseFloat(row.querySelector('.itin-price')?.value) || 0;
        var ref   = row.querySelector('.itin-ref')?.value || '';
        harvested[day].push({name, type, price, ref});
    });
    // Merge harvested into itinData (overwrite days that exist in DOM)
    Object.keys(harvested).forEach(function(day) { itinData[day] = harvested[day]; });

    const container = document.getElementById('itin-container');
    if (!container) return;
    let html = '';

    const typeMeta = {
        entrance:   {bg:'#fef9c3', border:'#fde68a', c:'#92400e', label:'Entrance'},
        activity:   {bg:'#dcfce7', border:'#bbf7d0', c:'#166534', label:'Activity'},
        restaurant: {bg:'#fce7f3', border:'#fbcfe8', c:'#9d174d', label:'Restaurant'},
        manual:     {bg:'#f1f5f9', border:'#e2e8f0', c:'#475569', label:'Manual'},
    };

    for (let d = 1; d <= n; d++) {
        const items    = itinData[d] || [];
        const dayTotal = items.reduce((s, i) => s + (parseFloat(i.price) || 0), 0);
        html += '<div style="margin-bottom:10px;background:white;border-radius:8px;border:1px solid #e8ecf0;overflow:hidden">'
            + '<div style="background:#374151;padding:8px 14px;display:flex;justify-content:space-between;align-items:center">'
            + '<span style="color:white;font-weight:800;font-size:12.5px">Day ' + d
            + (dayTotal > 0 ? ' <span style="opacity:.6;font-size:11px;margin-left:6px">IDR ' + fmt(dayTotal) + '/pax</span>' : '')
            + '</span><div style="display:flex;gap:4px">'
            + '<button type="button" onclick="openSearch(' + d + ',\'entrance\')" style="background:#fef9c3;color:#92400e;border:none;border-radius:3px;padding:2px 7px;cursor:pointer;font-size:10px;font-weight:700">+Entrance</button>'
            + '<button type="button" onclick="openSearch(' + d + ',\'activity\')" style="background:#dcfce7;color:#166534;border:none;border-radius:3px;padding:2px 7px;cursor:pointer;font-size:10px;font-weight:700">+Activity</button>'
            + '<button type="button" onclick="openSearch(' + d + ',\'restaurant\')" style="background:#fce7f3;color:#9d174d;border:none;border-radius:3px;padding:2px 7px;cursor:pointer;font-size:10px;font-weight:700">+Restaurant</button>'
            + '<button type="button" onclick="addManualItin(' + d + ')" style="background:rgba(255,255,255,.15);color:white;border:1px solid rgba(255,255,255,.3);border-radius:3px;padding:2px 7px;cursor:pointer;font-size:10px;font-weight:700">+Manual</button>'
            + '</div></div><div id="day-' + d + '-items">'
            + (items.length === 0 ? '<div style="padding:10px 14px;color:#adb5bd;font-size:11.5px;text-align:center">Belum ada item</div>' : items.map(function(it){ return renderItinRow(d, it); }).join(''))
            + '</div></div>';
    }

    container.innerHTML = html;
    syncItinHiddenInputs();
    recalcVariable();
}

function renderItinRow(day, item) {
    const typeMeta = {
        entrance:   {bg:'#fef9c3', c:'#92400e'},
        activity:   {bg:'#dcfce7', c:'#166534'},
        restaurant: {bg:'#fce7f3', c:'#9d174d'},
        manual:     {bg:'#f1f5f9', c:'#475569'},
    };
    const tc = typeMeta[item.type] || typeMeta.manual;
    const label = {entrance:'Entrance', activity:'Activity', restaurant:'Restaurant', manual:'Manual'}[item.type] || item.type;
    return '<div class="itin-row" data-day="' + day + '" style="display:flex;align-items:center;gap:7px;padding:6px 12px;border-bottom:1px solid #f0f2f5">'
        + '<input type="hidden" class="itin-type" value="' + (item.type||'manual') + '">'
        + '<input type="hidden" class="itin-ref"  value="' + (item.ref||'') + '">'
        + '<span style="font-size:9px;padding:2px 5px;border-radius:3px;font-weight:700;white-space:nowrap;background:' + tc.bg + ';color:' + tc.c + '">' + label + '</span>'
        + '<input type="text" class="itin-name" value="' + (item.name||'').replace(/"/g,'&quot;') + '" placeholder="Nama item" style="flex:1;border:1px solid #e8ecf0;border-radius:5px;padding:4px 8px;font-size:12.5px" oninput="syncItinHiddenInputs()">'
        + '<input type="number" class="itin-price" value="' + (item.price||0) + '" style="width:110px;border:1px solid #e8ecf0;border-radius:5px;padding:4px 8px;font-size:12.5px;text-align:right" oninput="recalcVariable();syncItinHiddenInputs()">'
        + '<button type="button" onclick="removeItinRow(this)" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px;line-height:1;padding:0 2px">\xd7</button>'
        + '</div>';}

function removeItinRow(btn) {
    const row = btn.closest('.itin-row');
    const day = parseInt(row.dataset.day);
    row.remove();
    // Remove from itinData
    if (itinData[day]) {
        // Rebuild from remaining DOM for this day
        itinData[day] = [];
        document.querySelectorAll('.itin-row[data-day="' + day + '"]').forEach(function(r) {
            const name  = r.querySelector('.itin-name')?.value || '';
            const type  = r.querySelector('.itin-type')?.value || 'manual';
            const price = parseFloat(r.querySelector('.itin-price')?.value) || 0;
            const ref   = r.querySelector('.itin-ref')?.value || '';
            if (name) itinData[day].push({name, type, price, ref});
        });
        if (itinData[day].length === 0) {
            const container = document.getElementById('day-' + day + '-items');
            if (container && container.querySelectorAll('.itin-row').length === 0) {
                container.innerHTML = '<div style="padding:10px 14px;color:#adb5bd;font-size:11.5px;text-align:center">Belum ada item</div>';
            }
        }
    }
    syncItinHiddenInputs();
    recalcVariable();
}

function addManualItin(day) {
    const container = document.getElementById('day-' + day + '-items');
    if (!container) return;
    const empty = container.querySelector('div[style*="text-align:center"]');
    if (empty) empty.remove();
    container.insertAdjacentHTML('beforeend', renderItinRow(day, {name:'', type:'manual', price:0, ref:''}));
    syncItinHiddenInputs();
    recalcVariable();
}

// Sync DOM itinerary rows → hidden inputs for form POST
function syncItinHiddenInputs() {
    const container = document.getElementById('itin-hidden-inputs');
    if (!container) return;
    let html = '';
    document.querySelectorAll('.itin-row').forEach(row => {
        const day   = row.dataset.day;
        const name  = row.querySelector('.itin-name')?.value || '';
        const type  = row.querySelector('.itin-type')?.value || 'manual';
        const price = row.querySelector('.itin-price')?.value || 0;
        const ref   = row.querySelector('.itin-ref')?.value || '';
        html += '<input type="hidden" name="itin_days[]"   value="' + day + '">';
        html += '<input type="hidden" name="itin_names[]"  value="' + (name||'').replace(/"/g,'&quot;') + '">';
        html += '<input type="hidden" name="itin_types[]"  value="' + (type||'manual') + '">';
        html += '<input type="hidden" name="itin_prices[]" value="' + (price||0) + '">';
        html += '<input type="hidden" name="itin_ref_ids[]" value="' + (ref||'') + '">';
    });
    container.innerHTML = html;
}

function recalcVariable() {
    const pax = Math.max(parseInt(document.getElementById('f-pax')?.value) || 1, 1);

    // Collect all itin rows, sum per type
    let totalVariable = 0;
    const byType = {entrance:0, activity:0, restaurant:0, manual:0};
    document.querySelectorAll('.itin-row').forEach(row => {
        const price = parseFloat(row.querySelector('.itin-price')?.value) || 0;
        const type  = row.querySelector('.itin-type')?.value || 'manual';
        totalVariable += price;
        byType[type] = (byType[type] || 0) + price;
    });

    // Update variable summary breakdown
    const breakdownEl = document.getElementById('sum-itin-breakdown');
    if (breakdownEl) {
        const lines = [];
        if (byType.entrance   > 0) lines.push('<div class="flex justify-between"><span>Entrance Fees</span><span class="font-mono">' + fmt(byType.entrance) + '</span></div>');
        if (byType.activity   > 0) lines.push('<div class="flex justify-between"><span>Activities</span><span class="font-mono">' + fmt(byType.activity) + '</span></div>');
        if (byType.restaurant > 0) lines.push('<div class="flex justify-between"><span>Restaurants</span><span class="font-mono">' + fmt(byType.restaurant) + '</span></div>');
        if (byType.manual     > 0) lines.push('<div class="flex justify-between"><span>Manual Items</span><span class="font-mono">' + fmt(byType.manual) + '</span></div>');
        breakdownEl.innerHTML = lines.length ? lines.join('') : '<span class="text-xs italic">Belum ada item</span>';
    }
    if (document.getElementById('sum-variable-total'))
        document.getElementById('sum-variable-total').textContent = fmt(totalVariable);

    // Fixed cost per pax from window.fcGrandPerPax
    const fixedPerPax = window.fcGrandPerPax || 0;
    const pax2 = pax;

    // Update fixed cost summary breakdown
    const tryGet = id => parseFloat(document.getElementById(id)?.value) || 0;
    const transportTotal = (() => {
        const manual = tryGet('fc-transport-manual');
        if (manual > 0) return manual;
        let t = 0;
        document.querySelectorAll('.transport-row').forEach(tr => {
            t += (parseFloat(tr.querySelector('.tr-price')?.value)||0) * (parseFloat(tr.querySelector('.tr-qty')?.value)||1);
        });
        if (t === 0) {
            try { JSON.parse(document.getElementById('fc-transport-json')?.value||'[]').forEach(r => t += (r.price||0)*(r.qty||1)); } catch(e){}
        }
        return t;
    })();
    const guideTotal = (() => {
        const manual = tryGet('fc-guide-manual');
        if (manual > 0) return manual;
        let t = 0;
        document.querySelectorAll('.guide-row').forEach(tr => {
            t += (parseFloat(tr.querySelector('.gd-rate')?.value)||0) * (parseFloat(tr.querySelector('.gd-qty')?.value)||1);
        });
        if (t === 0) {
            try { JSON.parse(document.getElementById('fc-guide-json')?.value||'[]').forEach(r => t += (r.rate||0)*(r.qty||1)); } catch(e){}
        }
        return t;
    })();
    const mwTotal = tryGet('fc-mw-amount');
    const fgTotal = tryGet('fc-fg-amount');
    const gaTotal = tryGet('fc-guide-allowance');
    const ltTotal = tryGet('fc-luggage-truck');
    const fixedSubtotal = transportTotal + guideTotal + mwTotal + fgTotal + gaTotal + ltTotal;

    // Update summary panel
    const gS = id => document.getElementById(id);
    const fixedPerPax2 = fixedSubtotal / pax2;
    if (gS('sum-fixed-total'))   gS('sum-fixed-total').textContent   = fmt(fixedPerPax2);
    if (gS('sum-variable-total'))gS('sum-variable-total').textContent= fmt(totalVariable);

    const totalIDR = fixedPerPax2 + totalVariable;
    if (gS('sum-total-idr')) gS('sum-total-idr').textContent = 'IDR ' + fmt(totalIDR);

    // Store for live recalc
    window._totalIDR = totalIDR;

    // Sync live input fields from master inputs (only if not already focused)
    const rateInput   = gS('sum-rate-input');
    const marginInput = gS('sum-margin-input');
    if (rateInput   && document.activeElement !== rateInput)
        rateInput.value   = parseFloat(gS('f-rate-usd')?.value) || 16000;
    if (marginInput && document.activeElement !== marginInput)
        marginInput.value = parseFloat(gS('f-margin')?.value) || 0.85;

    recalcLASummary();

    // Calc tab compat
    const cLaGroup = gS('c-la-group');   if(cLaGroup)  cLaGroup.textContent  = 'IDR ' + fmt(fixedSubtotal);
    const cLaPax   = gS('c-la-pax');     if(cLaPax)    cLaPax.textContent    = fmt(fixedPerPax2) + '/pax';
    const cItin    = gS('c-itin');        if(cItin)     cItin.textContent     = 'IDR ' + fmt(totalVariable);
    const cTotalLa = gS('c-total-la');   if(cTotalLa)  cTotalLa.textContent  = 'IDR ' + fmt(totalIDR);
}

// Live recalc from summary panel inputs (or master inputs)
function recalcLASummary() {
    const totalIDR = window._totalIDR || 0;
    const rateUSD  = parseFloat(document.getElementById('f-rate-usd')?.value) || 16000;
    const margin   = parseFloat(document.getElementById('f-margin')?.value) || 0.85;

    const totalUSD     = rateUSD > 0 ? totalIDR / rateUSD : 0;
    const laWithMargin = margin > 0   ? totalUSD / margin  : 0;

    const gS = id => document.getElementById(id);
    if (gS('sum-rate-display2'))  gS('sum-rate-display2').textContent  = fmt(rateUSD);
    if (gS('sum-margin-display2'))gS('sum-margin-display2').textContent= margin;
    if (gS('sum-total-usd'))      gS('sum-total-usd').textContent      = 'USD ' + totalUSD.toFixed(2);
    if (gS('sum-la-with-margin')) gS('sum-la-with-margin').textContent = 'USD ' + laWithMargin.toFixed(2);
}

let searchCtx = {};
function openSearch(day, type) {
    searchCtx = {day, type};
    document.getElementById('search-modal-title').textContent = 'Add ' + type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('search-input').value = '';
    document.getElementById('search-modal').style.display = 'flex';
    renderSearchList('');
    setTimeout(function(){ document.getElementById('search-input').focus(); }, 100);
}
function closeSearch() { document.getElementById('search-modal').style.display = 'none'; }

// Store filtered results so onclick can reference by index (avoids escaping issues)
let _searchResults = [];

function renderSearchList(q) {
    const src = searchCtx.type === 'entrance'   ? entrances
              : searchCtx.type === 'activity'    ? activities
              : searchCtx.type === 'restaurant'  ? restaurants
              : [];
    _searchResults = src.filter(x => x.name.toLowerCase().includes(q.toLowerCase()));
    const list = document.getElementById('search-list');
    if (!_searchResults.length) {
        list.innerHTML = '<p style="padding:12px;color:#adb5bd;text-align:center;font-size:13px">No results</p>';
        return;
    }
    list.innerHTML = _searchResults.map(function(item, i) {
        return '<div onclick="addFromSearchIdx(' + i + ')"'
            + ' style="padding:9px 12px;border-radius:7px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:background .15s"'
            + ' class="search-result-item">'
            + '<span style="font-weight:700;font-size:13px">' + item.name + '</span>'
            + (item.price ? '<span style="font-weight:800;color:#059669;font-size:13px">IDR ' + fmt(item.price) + '</span>' : '')
            + '</div>';
    }).join('');
}

function addFromSearchIdx(i) {
    const item = _searchResults[i];
    if (!item) return;
    if (searchCtx.type === 'restaurant') {
        openRestaurantMenuPicker(item.id, item.name, searchCtx.day);
    } else {
        addFromSearch(item.id, item.name, item.price || 0);
    }
}

// ── Restaurant Menu Picker (saat add restaurant di itin) ──────
var _restPickerCtx = {};

function openRestaurantMenuPicker(contractId, restaurantName, day) {
    _restPickerCtx = {contractId, restaurantName, day};
    closeSearch();
    var modal = document.getElementById('modal-rest-menu');
    if (!modal) return;
    modal.style.display = 'flex';
    document.getElementById('rest-picker-name').textContent = restaurantName;
    document.getElementById('rest-picker-menu-select').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('rest-picker-info').innerHTML = '';
    document.getElementById('rest-picker-price').value = 0;

    fetch('/tour-packages-api/restaurant-menus?contract_id=' + contractId)
        .then(function(r){ return r.json(); })
        .then(function(menus) {
            var sel = document.getElementById('rest-picker-menu-select');
            sel.innerHTML = '<option value="">— Pilih Menu —</option>';
            menus.forEach(function(m) {
                var o = document.createElement('option');
                o.value = m.id;
                o.textContent = m.menu_name;
                o.dataset.serving  = m.serving_style || '';
                o.dataset.price    = m.adult_price   || 0;
                o.dataset.details  = m.menu_details  || '';
                sel.appendChild(o);
            });
            if (menus.length) { sel.selectedIndex = 1; onRestPickerMenuChange(); }
        }).catch(function(){
            document.getElementById('rest-picker-menu-select').innerHTML = '<option value="">Gagal load menu</option>';
        });
}

function onRestPickerMenuChange() {
    var sel = document.getElementById('rest-picker-menu-select');
    var opt = sel.options[sel.selectedIndex];
    var infoEl = document.getElementById('rest-picker-info');
    if (!opt || !opt.value) { infoEl.innerHTML = ''; return; }
    var servingMap = {set_menu:'Set Menu Per Person', family_set:'Family Sharing', buffet:'Buffet'};
    var serving = servingMap[opt.dataset.serving] || opt.dataset.serving || '';
    infoEl.innerHTML = '<div class="grid grid-cols-2 gap-2 mt-2 text-xs">'
        + '<div><span class="text-gray-400">Serving Style: </span><span class="font-semibold">' + serving + '</span></div>'
        + '<div><span class="text-gray-400">Price/pax: </span><span class="font-semibold font-mono">IDR ' + fmt(opt.dataset.price) + '</span></div>'
        + '</div>'
        + (opt.dataset.details ? '<div class="mt-2 text-xs text-gray-500 bg-gray-50 rounded p-2 max-h-32 overflow-y-auto whitespace-pre-wrap">' + opt.dataset.details + '</div>' : '');
    document.getElementById('rest-picker-price').value = opt.dataset.price || 0;
}

function confirmRestMenuPicker() {
    var sel   = document.getElementById('rest-picker-menu-select');
    var opt   = sel.options[sel.selectedIndex];
    var price = parseFloat(document.getElementById('rest-picker-price').value) || 0;
    var ctx   = _restPickerCtx;

    closeRestMenuPicker();

    var day       = ctx.day;
    var type      = 'restaurant';
    var name      = ctx.restaurantName;
    var id        = ctx.contractId;
    var container = document.getElementById('day-' + day + '-items');
    var empty     = container?.querySelector('div[style*="text-align:center"]');
    if (empty) empty.remove();
    container?.insertAdjacentHTML('beforeend', renderItinRow(day, {name, type, price, ref: id}));

    // Pre-save menu selection to _menuData
    if (opt && opt.value) {
        var servingMap = {set_menu:'Set Menu Per Person', family_set:'Family Sharing', buffet:'Buffet'};
        if (!_menuData[id]) _menuData[id] = {};
        _menuData[id].menu_id       = opt.value;
        _menuData[id].menu_name     = opt.textContent;
        _menuData[id].serving_style = servingMap[opt.dataset.serving] || opt.dataset.serving || '';
        _menuData[id].adult_price   = opt.dataset.price || 0;
        _menuData[id].menu_details  = opt.dataset.details || '';
        syncMenuData();
    }

    syncItinHiddenInputs();
    recalcVariable();
}

function closeRestMenuPicker() {
    var modal = document.getElementById('modal-rest-menu');
    if (modal) modal.style.display = 'none';
}

function addFromSearch(id, name, price) {
    const day  = searchCtx.day;
    const type = searchCtx.type;
    const container = document.getElementById('day-' + day + '-items');
    const empty = container?.querySelector('div[style*="text-align:center"]');
    if (empty) empty.remove();
    container?.insertAdjacentHTML('beforeend', renderItinRow(day, {name, type, price, ref: id}));
    closeSearch();
    syncItinHiddenInputs();
    recalcVariable();
}

function addHotelRow() {
    const container = document.getElementById('hotels-container');
    const idx = container.querySelectorAll('.hotel-row').length + 1;
    const row = document.createElement('div');
    row.className = 'hotel-row border border-gray-200 rounded-lg mb-3 overflow-hidden';
    row.dataset.contractId = '';
    row.dataset.surchargeNights = '0';
    row.dataset.surchargeRate   = '0';
    row.innerHTML =
        '<div class="bg-gray-700 px-4 py-2 flex justify-between items-center">'
        + '<span class="text-xs font-bold text-white uppercase">Option ' + idx + '</span>'
        + '<button type="button" onclick="removeHotelRow(this)" class="text-xs text-red-400 hover:text-red-200 font-semibold">Remove</button>'
        + '</div>'
        + '<div class="p-4 grid grid-cols-4 gap-3 bg-gray-50">'
        + '<div class="col-span-2">'
        + '<label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Hotel</label>'
        + '<div class="relative">'
        + '<input type="text" name="hotel_names[]" class="w-full border border-gray-300 rounded px-3 py-2 text-sm hotel-name-input cursor-pointer bg-white" placeholder="Klik untuk pilih hotel..." readonly onclick="openHotelPicker(this.closest(\'.hotel-row\'))">'
        + '<input type="hidden" name="hotel_contract_ids[]" value="">'
        + '</div>'
        + '<div class="mt-1 hotel-surcharge-info text-xs text-orange-600 font-semibold hidden"></div>'
        + '</div>'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Room Type</label>'
        + '<select name="hotel_room_types[]" class="w-full border border-gray-300 rounded px-2 py-2 text-sm hotel-type-select" onchange="recalcHotel()">'
        + '<option value="">— pilih hotel dulu —</option></select></div>'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Harga/Malam (IDR)</label>'
        + '<input type="number" name="hotel_room_rates[]" value="0" class="w-full border border-gray-300 rounded px-2 py-2 text-sm hotel-rate-input" oninput="recalcHotel()"></div>'
        + '</div>'
        + '<div class="px-4 pb-4 pt-0 grid grid-cols-4 gap-3 bg-gray-50 border-t border-gray-100">'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Extra Bed (IDR/malam)</label>'
        + '<input type="number" name="hotel_extra_bed[]" value="0" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-extra-bed" oninput="recalcHotel()"></div>'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">HD. Meeting (IDR)</label>'
        + '<input type="number" name="hotel_hd_meeting[]" value="0" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-hd-meeting" oninput="recalcHotel()"></div>'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">FD. Meeting (IDR)</label>'
        + '<input type="number" name="hotel_fd_meeting[]" value="0" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-fd-meeting" oninput="recalcHotel()"></div>'
        + '<div><label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Dinner (IDR/pax)</label>'
        + '<input type="number" name="hotel_dinner[]" value="0" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm hotel-dinner" oninput="recalcHotel()"></div>'
        + '</div>'
        + '<input type="hidden" name="hotel_nights[]" value="0">'
        + '<input type="hidden" name="hotel_surcharge_nights[]" value="0">'
        + '<input type="hidden" name="hotel_surcharge_rates[]" value="0">';
    container.appendChild(row);
    recalcHotel();
}

function removeHotelRow(btn) {
    btn.closest('.hotel-row').remove();
    recalcHotel();
}

// ── Hotel Picker Modal ────────────────────────────────────────────────────────
var _hotelPickerRow = null;
var _hotelPickerResults = [];

function openHotelPicker(row) {
    _hotelPickerRow = row;
    var destination = (document.getElementById('f-destination')?.value || '').toLowerCase();
    // Filter by destination
    _hotelPickerResults = hotelContracts.filter(function(h) {
        var hd = (h.destination || '').toLowerCase();
        return !destination || !hd || hd.includes(destination) || destination.includes(hd);
    });
    document.getElementById('hotel-picker-search').value = '';
    renderHotelPickerList('');
    document.getElementById('modal-hotel-picker').style.display = 'flex';
    setTimeout(function() { document.getElementById('hotel-picker-search').focus(); }, 100);
}

function closeHotelPicker() {
    document.getElementById('modal-hotel-picker').style.display = 'none';
    _hotelPickerRow = null;
}

function renderHotelPickerList(q) {
    var dest = (document.getElementById('f-destination')?.value || '').toLowerCase();
    var filtered = hotelContracts.filter(function(h) {
        var hd = (h.destination || '').toLowerCase();
        var matchDest = !dest || !hd || hd.includes(dest) || dest.includes(hd);
        var matchQ    = !q || h.name.toLowerCase().includes(q.toLowerCase());
        return matchDest && matchQ;
    });
    _hotelPickerResults = filtered;
    var list = document.getElementById('hotel-picker-list');
    if (!filtered.length) {
        list.innerHTML = '<p style="color:#adb5bd;text-align:center;padding:20px;font-size:13px">Tidak ada hotel ditemukan</p>';
        return;
    }
    list.innerHTML = filtered.map(function(h, i) {
        return '<div onclick="selectHotelFromPicker(' + i + ')"'
            + ' style="padding:10px 12px;border-radius:7px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center"'
            + ' class="search-result-item">'
            + '<div>'
            + '<div style="font-weight:700;font-size:13px;color:#1f2937">' + h.name + '</div>'
            + (h.destination ? '<div style="font-size:11px;color:#9ca3af">' + h.destination + '</div>' : '')
            + '</div>'
            + '<i class="fa-solid fa-chevron-right" style="color:#d1d5db;font-size:11px"></i>'
            + '</div>';
    }).join('');
}

function selectHotelFromPicker(i) {
    var hotel = _hotelPickerResults[i];
    if (!hotel || !_hotelPickerRow) return;
    var row = _hotelPickerRow;
    // Set hotel name + contract id
    row.querySelector('.hotel-name-input').value = hotel.name;
    row.querySelector('input[name="hotel_contract_ids[]"]').value = hotel.id;
    row.dataset.contractId = hotel.id;
    closeHotelPicker();
    // Fetch room types
    fetchHotelRoomTypes(row, hotel.id);
    // Check surcharge
    checkSurcharge(row, hotel.id);
}

function fetchHotelRoomTypes(row, hid) {
    fetch('/tour-packages-api/hotel-rooms?id=' + hid)
        .then(function(r) { return r.json(); })
        .then(function(rooms) {
            var sel = row.querySelector('.hotel-type-select');
            var rateInput = row.querySelector('.hotel-rate-input');
            if (!sel) return;
            sel.innerHTML = '<option value="">— Pilih Room Type —</option>';
            rooms.forEach(function(rm) {
                var o = document.createElement('option');
                o.value = rm.type;
                o.textContent = rm.type;
                o.dataset.rate     = rm.rate      || 0;
                o.dataset.extraBed = rm.extra_bed || 0;
                sel.appendChild(o);
            });
            // Auto-select first and fill rate + extra_bed
            if (rooms.length) {
                sel.selectedIndex = 1;
                if (rateInput) rateInput.value = rooms[0].rate || 0;
                var extraBedInput = row.querySelector('.hotel-extra-bed');
                if (extraBedInput) extraBedInput.value = rooms[0].extra_bed || 0;
            }
            sel.onchange = function() {
                var opt = sel.options[sel.selectedIndex];
                if (rateInput && opt && opt.dataset.rate) rateInput.value = opt.dataset.rate;
                var extraBedInput = row.querySelector('.hotel-extra-bed');
                if (extraBedInput && opt && opt.dataset.extraBed !== undefined) extraBedInput.value = opt.dataset.extraBed;
                recalcHotel();
            };
            recalcHotel();
        })
        .catch(function() {});
}

function checkSurcharge(row, hid) {
    var periodFrom = document.getElementById('f-period-from')?.value;
    var periodTo   = document.getElementById('f-period-to')?.value;
    if (!periodFrom || !periodTo || !hid) return;
    fetch('/tour-packages-api/hotel-surcharge?id=' + hid + '&from=' + periodFrom + '&to=' + periodTo)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var info = row.querySelector('.hotel-surcharge-info');
            if (!info) return;
            var sn = (data && data.surcharge_nights > 0) ? data.surcharge_nights : 0;
            var sr = (data && data.surcharge_rate > 0) ? data.surcharge_rate : 0;
            if (sn > 0) {
                info.textContent = 'Surcharge: ' + sn + ' malam x IDR ' + fmt(sr) + ' = IDR ' + fmt(sn * sr);
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
            }
            row.dataset.surchargeNights = sn;
            row.dataset.surchargeRate   = sr;
            // Update hidden inputs for save
            var snInput = row.querySelector('input[name="hotel_surcharge_nights[]"]');
            var srInput = row.querySelector('input[name="hotel_surcharge_rates[]"]');
            if (snInput) snInput.value = sn;
            if (srInput) srInput.value = sr;
            recalcHotel();
        })
        .catch(function() {});
}

function recalcHotel() {
    var nights    = parseInt(document.getElementById('h-nights')?.value) || 0;
    var marginTWN = parseFloat(document.getElementById('h-margin-twn')?.value) || 0;
    var withTL    = parseInt(document.getElementById('h-with-tl')?.value) || 0;
    var rateUSD   = parseFloat(document.getElementById('f-rate-usd')?.value) || 16000;
    var rateMYR   = parseFloat(document.getElementById('f-rate-myr')?.value) || 4.70;
    var rateSGD   = parseFloat(document.getElementById('f-rate-sgd')?.value) || 1.35;
    var currency  = document.getElementById('f-currency')?.value || 'IDR';
    var margin    = parseFloat(document.getElementById('f-margin')?.value) || 0.85;
    var pax       = Math.max(parseInt(document.getElementById('f-pax')?.value) || 1, 1);
    var laUSD     = (window._totalIDR || 0) / rateUSD / margin;

    var fxRate, curSym;
    if      (currency === 'MYR') { fxRate = rateMYR; curSym = 'MYR'; }
    else if (currency === 'SGD') { fxRate = rateSGD; curSym = 'SGD'; }
    else if (currency === 'USD') { fxRate = 1;       curSym = 'USD'; }
    else                         { fxRate = rateUSD; curSym = 'IDR'; }

    console.log('[recalcHotel] currency='+currency+' fxRate='+fxRate+' laUSD='+laUSD.toFixed(4));

    var configInfo = document.getElementById('hotel-calc-config-info');
    if (configInfo) {
        configInfo.textContent = nights + ' malam  |  ' + currency
            + '  |  USD/IDR: ' + fmt(rateUSD)
            + (currency === 'MYR' ? '  |  USD/MYR: ' + rateMYR : '')
            + (currency === 'SGD' ? '  |  USD/SGD: ' + rateSGD : '')
            + '  |  LA/Pax USD: ' + laUSD.toFixed(2)
            + (withTL ? '  |  TL: Ya' : '');
    }

    var rows = document.querySelectorAll('.hotel-row');
    var tbl  = document.getElementById('hotel-calc-table');
    if (!rows.length || nights === 0) {
        if (tbl) tbl.innerHTML = '<p style="color:#6b7280;font-size:12px;padding:8px 0">Tambah hotel dan pastikan periode sudah diisi.</p>';
        return;
    }

    var hasHD = false, hasFD = false, hasDinner = false;
    rows.forEach(function(row) {
        if (parseFloat(row.querySelector('.hotel-hd-meeting')?.value) > 0) hasHD = true;
        if (parseFloat(row.querySelector('.hotel-fd-meeting')?.value) > 0) hasFD = true;
        if (parseFloat(row.querySelector('.hotel-dinner')?.value)     > 0) hasDinner = true;
    });

    var thStyle = 'padding:8px 10px;font-weight:700;white-space:nowrap;';
    var tableHtml = '<div style="overflow-x:auto">'
        + '<table style="width:100%;border-collapse:collapse;font-size:12px">'
        + '<thead><tr style="background:#374151;color:white;text-align:right">'
        + '<th style="' + thStyle + 'text-align:left">Nama Hotel</th>'
        + '<th style="' + thStyle + 'text-align:left">Room Type</th>'
        + '<th style="' + thStyle + '">SGL Occ.</th>'
        + '<th style="' + thStyle + '">TWN/DBL Share</th>'
        + '<th style="' + thStyle + '">TRP Share</th>';
    if (hasHD)     tableHtml += '<th style="' + thStyle + '">HD Meeting</th>';
    if (hasFD)     tableHtml += '<th style="' + thStyle + '">FD Meeting</th>';
    if (hasDinner) tableHtml += '<th style="' + thStyle + '">Dinner</th>';
    tableHtml += '</tr></thead><tbody>';

    rows.forEach(function(row, ri) {
        var hotelName = row.querySelector('.hotel-name-input')?.value || ('Hotel ' + (ri+1));
        var roomType  = row.querySelector('.hotel-type-select')?.value || '';
        var roomRate  = parseFloat(row.querySelector('.hotel-rate-input')?.value)  || 0;
        var extraBed  = parseFloat(row.querySelector('.hotel-extra-bed')?.value)   || 0;
        var hdMeeting = parseFloat(row.querySelector('.hotel-hd-meeting')?.value)  || 0;
        var fdMeeting = parseFloat(row.querySelector('.hotel-fd-meeting')?.value)  || 0;
        var dinner    = parseFloat(row.querySelector('.hotel-dinner')?.value)      || 0;
        var sNights   = parseInt(row.dataset.surchargeNights) || 0;
        var sRate     = parseFloat(row.dataset.surchargeRate) || 0;

        // STEP 1 — Room cost IDR (surcharge dihitung terpisah)
        var roomCost      = roomRate * nights;
        var surchargeCost = sRate * sNights;
        var roomTotal     = roomCost + surchargeCost;
        var extraBedTotal = (roomRate + extraBed) * nights + surchargeCost;

        // STEP 2 — Per occupancy IDR
        var sglIDR = roomTotal / 1;
        var twnIDR = (roomTotal + marginTWN) / 2;
        var trpIDR = extraBedTotal / 3;

        // STEP 3 — Convert to USD
        var sglUSD = sglIDR / rateUSD;
        var twnUSD = twnIDR / rateUSD;
        var trpUSD = trpIDR / rateUSD;

        // STEP 4 — TL cost: SGL_USD / pax
        var tlUSD = withTL ? (sglUSD / pax) : 0;

        // STEP 5 — Meeting & Dinner helpers (per pax)
        var hdUSD     = hdMeeting / pax / rateUSD;
        var fdUSD     = fdMeeting / pax / rateUSD;
        var dinnerUSD = dinner / rateUSD;

        // STEP 6 — Final = (roomUSD + laUSD + tlUSD) × fxRate
        function finalPrice(roomUSD) {
            var total = (roomUSD + laUSD + tlUSD) * fxRate;
            if (currency === 'IDR') return 'IDR ' + fmt(Math.round(total));
            return curSym + ' ' + total.toFixed(2);
        }
        function helperPrice(usd) {
            var val = usd * fxRate;
            if (currency === 'IDR') return 'IDR ' + fmt(Math.round(val));
            return curSym + ' ' + val.toFixed(2);
        }

        var bg = ri % 2 === 0 ? '#1f2937' : '#111827';
        var surchargeLabel = sNights > 0
            ? ' <span style="color:#fb923c;font-size:10px">(+' + sNights + ' mlm surcharge)</span>'
            : '';

        tableHtml += '<tr style="background:' + bg + ';border-bottom:1px solid #374151;text-align:right">'
            + '<td style="padding:8px 10px;color:white;font-weight:600;text-align:left">' + hotelName + surchargeLabel + '</td>'
            + '<td style="padding:8px 10px;color:#9ca3af;text-align:left">' + (roomType || '—') + '</td>'
            + '<td style="padding:8px 10px;color:#fde68a;font-weight:700">' + finalPrice(sglUSD) + '</td>'
            + '<td style="padding:8px 10px;color:#86efac;font-weight:700">' + finalPrice(twnUSD) + '</td>'
            + '<td style="padding:8px 10px;color:#93c5fd;font-weight:700">' + finalPrice(trpUSD) + '</td>';
        if (hasHD)     tableHtml += '<td style="padding:8px 10px;color:#c4b5fd">' + (hdMeeting > 0 ? helperPrice(hdUSD) : '—') + '</td>';
        if (hasFD)     tableHtml += '<td style="padding:8px 10px;color:#c4b5fd">' + (fdMeeting > 0 ? helperPrice(fdUSD) : '—') + '</td>';
        if (hasDinner) tableHtml += '<td style="padding:8px 10px;color:#c4b5fd">' + (dinner > 0    ? helperPrice(dinnerUSD) : '—') + '</td>';
        tableHtml += '</tr>';
    });

    tableHtml += '</tbody></table></div>';
    if (tbl) tbl.innerHTML = tableHtml;
}


// Close hotel picker on backdrop click
document.addEventListener('click', function(e) {
    var m = document.getElementById('modal-hotel-picker');
    if (m && e.target === m) closeHotelPicker();
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const flashTab = document.querySelector('meta[name="active-tab"]');
    if (flashTab) setTimeout(function(){ setTab(flashTab.content); }, 0);
    else if (urlParams.get('tab')) setTimeout(function(){ setTab(urlParams.get('tab')); }, 0);
    // Restore MW amount from hidden field (saved in DB)
    const savedMw = parseFloat(document.getElementById('fc-mw-amount')?.value) || 0;
    if (savedMw > 0) {
        if (document.getElementById('fc-mw-display')) document.getElementById('fc-mw-display').textContent = fmtMono(savedMw);
    }
    // Restore FG amount from hidden field
    const savedFg = parseFloat(document.getElementById('fc-fg-amount')?.value) || 0;
    if (savedFg > 0) {
        if (document.getElementById('fc-fg-display')) document.getElementById('fc-fg-display').textContent = fmtMono(savedFg);
    }
    initTransport();
    initGuide();
    recalcMw();
    recalcFg();
    if (existingItin.length) {
        existingItin.forEach(function(item) {
            if (!itinData[item.day]) itinData[item.day] = [];
            itinData[item.day].push({name:item.name, type:item.type, price:parseFloat(item.price)||0, ref:item.ref_id||''});
        });
    }
    buildDays();
    recalc();
    calcDuration();
    recalcLASummary();
    syncHotelNights();
    // Re-check surcharge on load for existing hotels
    setTimeout(function() {
        document.querySelectorAll('.hotel-row').forEach(function(row) {
            var hid = row.dataset.contractId;
            if (hid) checkSurcharge(row, hid);
        });
        recalcHotel();
    }, 200);
});

// ── Quill Rich Text Editors ──────────────────────────────────
function initQuillEditors() {
    if (typeof Quill === 'undefined') {
        setTimeout(initQuillEditors, 200);
        return;
    }
    var toolbarOptions = [
        ['bold','italic','underline'],
        [{'list':'ordered'},{'list':'bullet'}],
        ['clean']
    ];
    if (!window._quillInclusion && document.getElementById('editor-inclusion'))
        window._quillInclusion = new Quill('#editor-inclusion', {theme:'snow', modules:{toolbar:toolbarOptions}});
    if (!window._quillExclusion && document.getElementById('editor-exclusion'))
        window._quillExclusion = new Quill('#editor-exclusion', {theme:'snow', modules:{toolbar:toolbarOptions}});
    if (!window._quillTnc && document.getElementById('editor-tnc'))
        window._quillTnc       = new Quill('#editor-tnc',       {theme:'snow', modules:{toolbar:toolbarOptions}});
    if (!window._quillItinerary && document.getElementById('editor-itinerary'))
        window._quillItinerary = new Quill('#editor-itinerary', {theme:'snow', modules:{toolbar:toolbarOptions}});
    if (!window._quillMenu && document.getElementById('editor-menu'))
        window._quillMenu      = new Quill('#editor-menu',      {theme:'snow', modules:{toolbar:toolbarOptions}});

    // Default T&C
    window._quillTnc.setContents([
        {insert: 'The above rate are quoted in MYR currency, net/person\n'},
        {insert: 'There is no accommodation, activities, and transportation have been blocked until we receive the confirmation from your side\n'},
        {insert: 'The room will subject to availability at the time required\n'},
        {insert: 'Once the proposal is confirmed, 50% deposit should be settled at least 30 days prior the arrival, and balance payment should be settled at least a week prior the arrival\n'},
        {insert: 'Once the proposal is confirmed, cancellation policy will apply for any cancellation made within certain period\n'},
        {insert: 'The above rate is correct at the time proposed, and subject to change without prior notice\n'},
    ]);

    // Sync hidden inputs on change
    if (window._quillInclusion) window._quillInclusion.on('text-change', function() {
        document.getElementById('prop-inclusion-hidden').value = window._quillInclusion.root.innerHTML;
    });
    if (window._quillExclusion) window._quillExclusion.on('text-change', function() {
        document.getElementById('prop-exclusion-hidden').value = window._quillExclusion.root.innerHTML;
    });
    if (window._quillTnc) window._quillTnc.on('text-change', function() {
        document.getElementById('prop-tnc-hidden').value = window._quillTnc.root.innerHTML;
    });
    if (window._quillItinerary) window._quillItinerary.on('text-change', function() {
        document.getElementById('prop-itinerary-hidden').value = window._quillItinerary.root.innerHTML;
    });
    if (window._quillMenu) window._quillMenu.on('text-change', function() {
        document.getElementById('prop-menu-hidden').value = window._quillMenu.root.innerHTML;
    });
}
document.addEventListener('DOMContentLoaded', function() {
    var s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js';
    s.onload = initQuillEditors;
    document.head.appendChild(s);
});



// ── Proposed Itinerary ───────────────────────────────────────
var _itinBriefs = {};
(function(){
    var raw = @json($package?->prop_itin_briefs ?? null);
    if (raw && typeof raw === 'object' && !Array.isArray(raw)) _itinBriefs = raw;
    else if (typeof raw === 'string') { try { var p=JSON.parse(raw); if(p && !Array.isArray(p)) _itinBriefs=p; } catch(e){} }
})(); // loaded from DB
var GEMINI_API_KEY = '';

function renderItinTable() {
    var tbl = document.getElementById('prop-itin-table');
    if (!tbl) return;

    // Read from live DOM itin rows first
    var days = {};
    document.querySelectorAll('.itin-row').forEach(function(row) {
        var day   = parseInt(row.dataset.day);
        var name  = row.querySelector('.itin-name') ? row.querySelector('.itin-name').value : '';
        var type  = row.querySelector('.itin-type') ? row.querySelector('.itin-type').value : 'manual';
        var ref   = row.querySelector('.itin-ref')  ? row.querySelector('.itin-ref').value  : '';
        if (!name) return;
        if (!days[day]) days[day] = [];
        days[day].push({day: day, name: name, type: type, ref_id: ref});
    });

    // Fallback ke existingItin
    if (!Object.keys(days).length) {
        existingItin.forEach(function(item) {
            if (!days[item.day]) days[item.day] = [];
            days[item.day].push(item);
        });
    }

    if (!Object.keys(days).length) {
        tbl.innerHTML = '<p class="text-xs text-gray-400 italic">Belum ada itinerary. Isi di tab LA Cost terlebih dahulu.</p>';
        return;
    }

    var html = '<table style="width:100%;border-collapse:collapse;font-size:13px;border:1px solid #e5e7eb">'
        + '<thead><tr style="background:#1f2937;color:white">'
        + '<th style="padding:10px 14px;text-align:left;font-weight:700;width:80px">Day</th>'
        + '<th style="padding:10px 14px;text-align:left;font-weight:700">Itinerary</th>'
        + '<th style="padding:10px 14px;text-align:left;font-weight:700;width:90px">Remarks</th>'
        + '</tr></thead><tbody>';

    Object.keys(days).sort(function(a,b){return parseInt(a)-parseInt(b);}).forEach(function(day) {
        var items = days[day];
        html += '<tr style="border-bottom:1px solid #e5e7eb;vertical-align:top">'
            + '<td style="padding:12px 14px;font-weight:700;color:#374151;white-space:nowrap">Day ' + day + '</td>'
            + '<td style="padding:12px 14px">';

        items.forEach(function(item) {
            var isActivity = item.type === 'entrance' || item.type === 'activity';
            var brief = _itinBriefs[item.ref_id] || '';
            var nameStyle = 'font-weight:700;color:#1f2937;margin-bottom:2px';
            html += '<div style="margin-bottom:10px">'
                + '<div style="' + nameStyle + '">- ' + item.name + '</div>';
            if (brief) {
                html += '<div style="color:#4b5563;font-size:12px;line-height:1.6;margin-left:12px;font-style:italic;text-align:justify" id="brief-text-' + item.ref_id + '">' + brief + '</div>';
            } else if (isActivity) {
                html += '<div style="margin-left:12px" id="brief-placeholder-' + item.ref_id + '">'
                    + '<button type="button" onclick="generateBrief(\'' + item.ref_id + '\',\'' + item.name.replace(/'/g,"\\'") + '\')"'
                    + ' style="font-size:11px;color:#6366f1;background:none;border:1px solid #c7d2fe;border-radius:4px;padding:2px 8px;cursor:pointer">'
                    + '<i class="fa-solid fa-wand-magic-sparkles" style="margin-right:4px"></i>Generate Brief</button>'
                    + '</div>';
            }
            html += '</div>';
        });

        html += '</td><td style="padding:12px 14px;color:#9ca3af;font-size:12px"></td></tr>';
    });

    html += '</tbody></table>';
    tbl.innerHTML = html;
    syncItinHidden();
}

function generateBrief(refId, name) {
    var placeholder = document.getElementById('brief-placeholder-' + refId);
    if (placeholder) placeholder.innerHTML = '<span style="font-size:11px;color:#9ca3af"><i class="fa-solid fa-spinner fa-spin" style="margin-right:4px"></i>Generating...</span>';

    var apiKey = 'server'; // handled server-side

    var prompt = 'Write a brief 3-5 sentence description for a tour itinerary about: "' + name + '". '
        + 'Make it engaging and informative for tourists. Focus on what visitors will experience. '
        + 'Do not use bullet points. Write in flowing paragraph style.';

    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('/tour-packages-api/generate-brief', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({name: name, api_key: apiKey})
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        console.log('[Gemini proxy]', data);
        if (data.text) {
            // Strip markdown headings and extra newlines
            var clean = data.text
                .replace(/^#+\s+.+\n*/gm, '')
                .replace(/\n{2,}/g, ' ')
                .replace(/\n/g, ' ')
                .trim();
            // Ensure _itinBriefs is object not array
            if (Array.isArray(_itinBriefs)) _itinBriefs = {};
            if (Array.isArray(_itinBriefs)) _itinBriefs = {};
            _itinBriefs[refId] = clean;
            // Sync immediately to hidden input
            var eb = document.getElementById('prop-itin-briefs-hidden');
            if (eb) eb.value = JSON.stringify(_itinBriefs);
            renderItinTable();
        } else {
            var errMsg = data.error || 'Failed';
            if (placeholder) placeholder.innerHTML = '<span style="color:#ef4444;font-size:11px">' + errMsg + '</span>';
        }
    })
    .catch(function(e) {
        console.error('[Gemini error]', e);
        if (placeholder) placeholder.innerHTML = '<span style="color:#ef4444;font-size:11px">Error: ' + e.message + '</span>';
    });
}

function generateAllBriefs() {
    if (Array.isArray(_itinBriefs)) _itinBriefs = {};
    var apiKey = 'server'; // handled server-side
    // Use live DOM rows instead of existingItin
    var allItems = [];
    document.querySelectorAll('.itin-row').forEach(function(row) {
        var type = row.querySelector('.itin-type') ? row.querySelector('.itin-type').value : '';
        var ref  = row.querySelector('.itin-ref')  ? row.querySelector('.itin-ref').value  : '';
        var name = row.querySelector('.itin-name') ? row.querySelector('.itin-name').value : '';
        if ((type === 'entrance' || type === 'activity') && ref && name) {
            allItems.push({ref_id: ref, name: name});
        }
    });
    var queue = allItems.filter(function(i){ return !_itinBriefs[i.ref_id]; });
    var idx = 0;
    function next() {
        if (idx >= queue.length) return;
        var item = queue[idx++];
        generateBrief(item.ref_id, item.name);
        setTimeout(next, 1200); // 1.2s delay to respect rate limit
    }
    next();
}

function showGeminiKeyPrompt(refId, name) {
    var key = ''; // API key handled server-side
    if (key) {
        GEMINI_API_KEY = key;
        var el = document.getElementById('gemini-api-key');
        if (el) el.value = key;
        if (refId) generateBrief(refId, name);
    }
}

function syncAllProposalFields() {
    // Sync Quill editors
    if (window._quillInclusion) document.getElementById("prop-inclusion-hidden").value = window._quillInclusion.root.innerHTML;
    if (window._quillExclusion) document.getElementById("prop-exclusion-hidden").value = window._quillExclusion.root.innerHTML;
    if (window._quillTnc)       document.getElementById("prop-tnc-hidden").value       = window._quillTnc.root.innerHTML;
    // Sync menu data (replaces prop-menu-hidden)
    syncMenuData();
    // Sync briefs — direct assign, no double stringify
    var eb = document.getElementById("prop-itin-briefs-hidden");
    if (eb) eb.value = JSON.stringify(_itinBriefs);
    // Sync custom tables
    syncCustomTables();
}

function syncItinHidden() {
    var el = document.getElementById('prop-itinerary-hidden');
    if (el) el.value = JSON.stringify({items: existingItin, briefs: _itinBriefs});
    // briefs synced in syncAllProposalFields only
}

// ── Accordion ────────────────────────────────────────────────
// ── Restaurant Menu ──────────────────────────────────────────
var _menuData = @json($package?->prop_menu ? (is_array($package->prop_menu) ? $package->prop_menu : json_decode($package->prop_menu, true)) : []); // {ref_id: {menu_id, menu_name, serving_style, adult_price, menu_details}}
if (!_menuData || Array.isArray(_menuData)) _menuData = {};

function renderMenuTable() {
    var container = document.getElementById('prop-menu-container');
    if (!container) return;

    // Group restaurants by day from existingItin (live DOM state)
    var byDay = {};
    var allItinRows = [];
    document.querySelectorAll('.itin-row').forEach(function(row) {
        var type = row.querySelector('.itin-type')?.value;
        if (type !== 'restaurant') return;
        var day   = parseInt(row.dataset.day);
        var name  = row.querySelector('.itin-name')?.value || '';
        var ref   = row.querySelector('.itin-ref')?.value || '';
        var price = parseFloat(row.querySelector('.itin-price')?.value) || 0;
        if (!byDay[day]) byDay[day] = [];
        byDay[day].push({day, name, ref_id: ref, price});
    });

    // Fallback ke existingItin kalau DOM belum dirender
    if (!Object.keys(byDay).length) {
        existingItin.forEach(function(item) {
            if (item.type !== 'restaurant') return;
            if (!byDay[item.day]) byDay[item.day] = [];
            byDay[item.day].push(item);
        });
    }

    if (!Object.keys(byDay).length) {
        container.innerHTML = '<p class="text-xs text-gray-400 italic">Belum ada restaurant di itinerary. Tambahkan di tab LA Cost.</p>';
        return;
    }

    var servingLabel = {set_menu:'Set Menu Per Person', family_set:'Family Sharing', buffet:'Buffet'};

    var html = '';
    Object.keys(byDay).sort(function(a,b){return parseInt(a)-parseInt(b);}).forEach(function(day) {
        var items = byDay[day];
        var cols  = Math.min(items.length, 2);
        html += '<div class="mb-4">';
        html += '<div class="bg-gray-800 text-white text-xs font-bold px-4 py-2 rounded-t-lg">Day ' + day + '</div>';
        html += '<div class="grid border border-gray-200 rounded-b-lg overflow-hidden" style="grid-template-columns:repeat(' + cols + ',1fr)">';

        items.forEach(function(item, idx) {
            var saved   = _menuData[item.ref_id] || {};
            var serving = saved.serving_style || '';
            var price   = saved.adult_price   || item.price || 0;
            var details = saved.menu_details  || '';
            var menuName = saved.menu_name    || '';
            var borderL = idx > 0 ? 'border-left:1px solid #e5e7eb;' : '';

            html += '<div style="padding:16px;background:white;' + borderL + '">';

            // Header: restaurant name + menu name
            html += '<div class="font-black text-gray-800 text-sm mb-1">' + item.name + '</div>';
            if (menuName) html += '<div class="text-xs text-indigo-600 font-semibold mb-2">' + menuName + '</div>';

            // Info row: serving style + price
            html += '<div class="flex gap-4 mb-3 text-xs">';
            if (serving) html += '<span class="bg-indigo-50 text-indigo-700 font-semibold px-2 py-1 rounded">' + serving + '</span>';
            if (price > 0) html += '<span class="text-gray-500">Meals budget: <span class="font-bold text-gray-800">IDR ' + fmt(price) + '/pax</span></span>';
            html += '</div>';

            // Menu details — render as HTML
            if (details) {
                html += '<div class="text-sm text-gray-700 leading-relaxed rest-details-html" data-ref="' + item.ref_id + '">' + details + '</div>';
            } else {
                html += '<div class="text-xs text-gray-400 italic rest-details-html" data-ref="' + item.ref_id + '">Menu details belum tersedia.</div>';
            }

            html += '</div>';
        });

        html += '</div></div>';
    });

    container.innerHTML = html;
    syncMenuData();
}

function loadRestaurantMenuOptions(contractId) { /* deprecated - menu picked at itin add time */ }

function onMenuSelect(sel) { /* deprecated */ }

function fillMenuFields(refId, opt) {
    var servingMap = {set_menu:'Set Menu Per Person', family_set:'Family Sharing', buffet:'Buffet'};
    if (!_menuData[refId]) _menuData[refId] = {};
    _menuData[refId].menu_id       = opt.value;
    _menuData[refId].menu_name     = opt.textContent;
    _menuData[refId].serving_style = servingMap[opt.dataset.serving] || opt.dataset.serving || '';
    _menuData[refId].adult_price   = opt.dataset.price || 0;
    _menuData[refId].menu_details  = opt.dataset.details || '';
}

function syncMenuData() {
    var el = document.getElementById('prop-menu-hidden');
    if (el) el.value = JSON.stringify(_menuData);
}

function toggleAccordion(id) {
    var sections = ['acc-rate','acc-itin','acc-menu'];
    sections.forEach(function(sid) {
        var body = document.getElementById(sid);
        var icon = document.getElementById(sid + '-icon');
        if (!body) return;
        if (sid === id) {
            var isOpen = !body.classList.contains('hidden');
            if (isOpen) {
                body.classList.add('hidden');
                if (icon) icon.style.transform = '';
            } else {
                body.classList.remove('hidden');
                if (icon) icon.style.transform = 'rotate(180deg)';
                // Init Quill editors if needed when section opens
                if (sid === 'acc-rate' || sid === 'acc-itin' || sid === 'acc-menu') {
                    setTimeout(initQuillEditors, 50);
                }
                if (sid === 'acc-itin') { setTimeout(function(){ buildDays(); setTimeout(renderItinTable, 50); }, 0); }
                if (sid === 'acc-menu') { setTimeout(renderMenuTable, 50); }
            }
        } else {
            body.classList.add('hidden');
            if (icon) icon.style.transform = '';
        }
    });
}

// ── Custom Price Tables (multiple, each with title) ──────────
var _customTables = [];

function addCustomTable(title, rows) {
    var id = Date.now();
    _customTables.push({id: id, title: title||'', rows: rows||[]});
    renderCustomTables();
}

function removeCustomTable(id) {
    _customTables = _customTables.filter(function(t){ return t.id !== id; });
    renderCustomTables();
}

function addRowToTable(tid) {
    var t = _customTables.find(function(t){ return t.id === tid; });
    if (t) { t.rows.push({id: Date.now(), desc:'', price:'', cur:'MYR'}); renderCustomTables(); }
}

function removeRowFromTable(tid, rid) {
    var t = _customTables.find(function(t){ return t.id === tid; });
    if (t) { t.rows = t.rows.filter(function(r){ return r.id !== rid; }); renderCustomTables(); }
}

function renderCustomTables() {
    var wrapper = document.getElementById('custom-tables-wrapper');
    if (!wrapper) return;
    if (_customTables.length === 0) {
        wrapper.innerHTML = '';
        syncCustomTables();
        return;
    }
    wrapper.innerHTML = _customTables.map(function(tbl, ti) {
        var rowsHtml = tbl.rows.length === 0
            ? '<tr><td colspan="4" style="padding:10px 14px;text-align:center;color:#9ca3af;font-size:12px;font-style:italic">Belum ada baris.</td></tr>'
            : tbl.rows.map(function(row, ri) {
                var bg = ri % 2 === 0 ? '#ffffff' : '#f9fafb';
                var opts = ['MYR','USD','IDR','SGD'].map(function(c){
                    return '<option value="' + c + '"' + (row.cur===c?' selected':'') + '>' + c + '</option>';
                }).join('');
                return '<tr style="background:' + bg + ';border-bottom:1px solid #e5e7eb">'
                    + '<td style="padding:8px 12px"><input type="text" value="' + row.desc.replace(/"/g,'&quot;') + '" oninput="_customTables['+ti+'].rows['+ri+'].desc=this.value;syncCustomTables()" placeholder="e.g. Optional Tour" style="width:100%;border:1px solid #e5e7eb;border-radius:4px;padding:4px 8px;font-size:13px;outline:none"></td>'
                    + '<td style="padding:8px 12px"><input type="text" value="' + row.price + '" oninput="_customTables['+ti+'].rows['+ri+'].price=this.value;syncCustomTables()" placeholder="0" style="width:100%;border:1px solid #e5e7eb;border-radius:4px;padding:4px 8px;font-size:13px;text-align:right;font-family:monospace;outline:none"></td>'
                    + '<td style="padding:8px 12px"><select oninput="_customTables['+ti+'].rows['+ri+'].cur=this.value;syncCustomTables()" style="border:1px solid #e5e7eb;border-radius:4px;padding:4px 8px;font-size:13px;outline:none">' + opts + '</select></td>'
                    + '<td style="padding:8px 12px;text-align:center"><button type="button" onclick="removeRowFromTable(' + tbl.id + ',' + row.id + ')" style="color:#f87171;background:none;border:none;cursor:pointer"><i class="fa-solid fa-trash" style="font-size:11px"></i></button></td>'
                    + '</tr>';
            }).join('');

        return '<div style="margin-bottom:16px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden">'
            + '<div style="background:#f9fafb;border-bottom:1px solid #e5e7eb;padding:10px 14px;display:flex;align-items:center;gap:10px;justify-content:space-between">'
            + '<div style="display:flex;align-items:center;gap:8px;flex:1">'
            + '<i class="fa-solid fa-table" style="color:#f59e0b;font-size:12px"></i>'
            + '<input type="text" value="' + tbl.title.replace(/"/g,'&quot;') + '" oninput="_customTables['+ti+'].title=this.value;syncCustomTables()" placeholder="Table title, e.g. Teambuilding Rate" style="flex:1;border:none;background:transparent;font-size:13px;font-weight:700;color:#374151;outline:none;min-width:200px">'
            + '</div>'
            + '<div style="display:flex;gap:8px">'
            + '<button type="button" onclick="addRowToTable(' + tbl.id + ')" style="background:#f59e0b;color:white;border:none;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer"><i class="fa-solid fa-plus" style="margin-right:4px"></i>Add Row</button>'
            + '<button type="button" onclick="removeCustomTable(' + tbl.id + ')" style="background:#fee2e2;color:#ef4444;border:none;border-radius:6px;padding:4px 10px;font-size:12px;cursor:pointer"><i class="fa-solid fa-trash"></i></button>'
            + '</div></div>'
            + '<table style="width:100%;border-collapse:collapse">'
            + '<thead><tr style="background:#1f2937;color:white;font-size:11px;text-transform:uppercase;letter-spacing:.05em">'
            + '<th style="padding:8px 12px;text-align:left;font-weight:700">Description</th>'
            + '<th style="padding:8px 12px;text-align:right;font-weight:700;min-width:120px">Price</th>'
            + '<th style="padding:8px 12px;text-align:left;font-weight:700;width:80px">Currency</th>'
            + '<th style="width:40px"></th>'
            + '</tr></thead>'
            + '<tbody>' + rowsHtml + '</tbody>'
            + '</table></div>';
    }).join('');
    syncCustomTables();
}

function syncCustomTables() {
    var el = document.getElementById('prop-custom-tables-hidden');
    if (el) el.value = JSON.stringify(_customTables);
}

</script>

<div id="modal-rest-menu" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);align-items:center;justify-content:center;z-index:1001"
     onclick="if(event.target===this)closeRestMenuPicker()">
    <div style="background:white;border-radius:12px;width:480px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.3)">
        <div style="background:#1f2937;padding:14px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
            <div>
                <div style="color:white;font-weight:800;font-size:14px">Pilih Menu Restaurant</div>
                <div id="rest-picker-name" style="color:#9ca3af;font-size:12px;margin-top:2px"></div>
            </div>
            <button onclick="closeRestMenuPicker()" style="background:none;border:none;color:rgba(255,255,255,.6);font-size:22px;cursor:pointer">×</button>
        </div>
        <div style="padding:16px 20px;overflow:auto;flex:1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Menu</label>
            <select id="rest-picker-menu-select" class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2" onchange="onRestPickerMenuChange()"></select>
            <div id="rest-picker-info"></div>
            <div class="mt-3">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Meals Budget/pax (IDR) <span class="font-normal normal-case text-gray-400">— bisa diedit</span></label>
                <input type="number" id="rest-picker-price" class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono">
            </div>
        </div>
        <div style="padding:12px 20px;border-top:1px solid #e8ecf0;display:flex;justify-content:flex-end;gap:8px;background:#f8fafc;border-radius:0 0 12px 12px;flex-shrink:0">
            <button type="button" onclick="closeRestMenuPicker()" class="text-xs text-gray-500 font-semibold px-4 py-2 rounded border border-gray-200 bg-white">Batal</button>
            <button type="button" onclick="confirmRestMenuPicker()" class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-2 rounded">
                <i class="fa-solid fa-check mr-1"></i> Tambah ke Itinerary
            </button>
        </div>
    </div>
</div>

<div id="search-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);align-items:center;justify-content:center;z-index:999"
     onclick="if(event.target===this)closeSearch()">
    <div style="background:white;border-radius:12px;padding:22px;width:440px;max-height:70vh;overflow:auto;box-shadow:0 25px 60px rgba(0,0,0,.25)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h3 id="search-modal-title" style="margin:0;font-size:15px;font-weight:800;color:#1f2937;text-transform:capitalize"></h3>
            <button onclick="closeSearch()" style="background:none;border:none;cursor:pointer;font-size:22px;color:#adb5bd">x</button>
        </div>
        <input id="search-input" oninput="renderSearchList(this.value)" placeholder="Search..."
            style="width:100%;border:1px solid #dde1e7;border-radius:8px;padding:9px 13px;font-size:14px;margin-bottom:10px;box-sizing:border-box;outline:none">
        <div id="search-list"></div>
    </div>
</div>
@endsection