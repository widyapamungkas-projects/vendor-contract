<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<title>Tour Proposal – {{ $package->package_code }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ── Reset ──────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', Arial, sans-serif; background: #e5e7eb; color: #1f2937; font-size: 13px; }

/* ── Toolbar (screen only) ───────────────── */
.toolbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    background: #1f2937; padding: 10px 24px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 2px 12px rgba(0,0,0,.3);
}
.toolbar-left { display: flex; align-items: center; gap: 12px; }
.toolbar-left a { color: #9ca3af; font-size: 12px; text-decoration: none; }
.toolbar-left a:hover { color: white; }
.toolbar-title { color: white; font-weight: 700; font-size: 14px; }
.toolbar-right { display: flex; gap: 8px; }
.btn-tool {
    padding: 7px 16px; border-radius: 6px; font-size: 12px; font-weight: 700;
    cursor: pointer; border: none; display: flex; align-items: center; gap: 6px;
}
.btn-print  { background: #f59e0b; color: white; }
.btn-print:hover  { background: #d97706; }
.btn-word   { background: #2563eb; color: white; }
.btn-word:hover   { background: #1d4ed8; }
.btn-back   { background: #374151; color: #d1d5db; }
.btn-back:hover   { background: #4b5563; }

/* ── Page wrapper ───────────────────────── */
.page-wrapper { padding: 72px 24px 40px; display: flex; flex-direction: column; align-items: center; gap: 0; }

/* ── A4 Page ────────────────────────────── */
.page {
    width: 210mm;
    min-height: 297mm;
    background: white;
    box-shadow: 0 4px 32px rgba(0,0,0,.18);
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

/* ── Header ─────────────────────────────── */
.doc-header {
    background: #1f2937;
    padding: 28px 36px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}
.doc-header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #ef4444, #8b5cf6);
}
.header-logo { display: flex; align-items: center; gap: 14px; }
.logo-icon {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: white;
}
.logo-text-main { color: white; font-size: 18px; font-weight: 900; letter-spacing: .5px; }
.logo-text-sub  { color: #9ca3af; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.header-right   { text-align: right; }
.header-doc-type { color: #f59e0b; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
.header-code     { color: white; font-size: 20px; font-weight: 900; margin-top: 4px; }
.header-date     { color: #6b7280; font-size: 10px; margin-top: 4px; }

/* ── Body ───────────────────────────────── */
.doc-body { padding: 28px 36px; flex: 1; }

/* ── Section ────────────────────────────── */
.section { margin-bottom: 28px; }
.section-title {
    font-size: 10px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;
    color: #6b7280; border-bottom: 2px solid #f3f4f6;
    padding-bottom: 6px; margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.section-title i { color: #f59e0b; font-size: 11px; }

/* ── Client Details ─────────────────────── */
.client-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 24px;
}
.client-row { display: flex; flex-direction: column; gap: 2px; }
.client-label { font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
.client-value { font-size: 13px; font-weight: 700; color: #1f2937; }
.pkg-name-banner {
    background: linear-gradient(135deg, #1f2937, #374151);
    color: white; text-align: center;
    padding: 10px 20px; border-radius: 8px;
    font-size: 15px; font-weight: 900; letter-spacing: .5px;
    margin-bottom: 14px;
}

/* ── Rate Table ─────────────────────────── */
.rate-table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
.rate-table thead tr { background: #1f2937; color: white; }
.rate-table th { padding: 8px 10px; text-align: center; font-weight: 700; font-size: 10px; letter-spacing: .5px; }
.rate-table th:first-child { text-align: left; }
.rate-table tbody tr { border-bottom: 1px solid #f3f4f6; }
.rate-table tbody tr:last-child { border-bottom: none; }
.rate-table td { padding: 9px 10px; }
.rate-table td:first-child { font-weight: 700; color: #374151; }
.rate-table td { text-align: center; color: #374151; }
.rate-num { font-weight: 800; color: #1f2937; font-family: monospace; font-size: 12px; }
.rate-currency { font-size: 9px; color: #9ca3af; margin-right: 3px; }
.rate-note { font-size: 10px; color: #9ca3af; margin-top: 6px; font-style: italic; }

/* ── Inclusion / Exclusion ──────────────── */
.incl-excl-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.incl-box, .excl-box {
    border-radius: 8px; padding: 14px 16px;
}
.incl-box { background: #f0fdf4; border: 1px solid #bbf7d0; }
.excl-box { background: #fff7ed; border: 1px solid #fed7aa; }
.incl-excl-title {
    font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
    display: flex; align-items: center; gap: 6px; margin-bottom: 10px;
}
.incl-excl-title.incl { color: #15803d; }
.incl-excl-title.excl { color: #c2410c; }
.incl-excl-content { font-size: 11.5px; color: #374151; line-height: 1.7; }
.incl-excl-content ul { padding-left: 16px; }
.incl-excl-content li { margin-bottom: 2px; }

/* ── Itinerary ───────────────────────────── */
.itin-table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
.itin-table thead tr { background: #1f2937; color: white; }
.itin-table th { padding: 8px 12px; text-align: left; font-weight: 700; font-size: 10px; letter-spacing: .5px; }
.itin-table tbody tr { border-bottom: 1px solid #f3f4f6; vertical-align: top; }
.itin-day-cell { padding: 12px; font-weight: 900; color: #374151; white-space: nowrap; width: 70px; }
.itin-day-badge {
    background: #1f2937; color: white;
    border-radius: 6px; padding: 3px 8px;
    font-size: 11px; font-weight: 800;
    display: inline-block;
}
.itin-content-cell { padding: 12px; }
.itin-item { margin-bottom: 10px; }
.itin-item:last-child { margin-bottom: 0; }
.itin-item-name { font-weight: 700; color: #1f2937; font-size: 12px; }
.itin-item-brief { color: #6b7280; font-size: 11px; line-height: 1.6; margin-top: 3px; font-style: italic; text-align: justify; }
.itin-remarks-cell { padding: 12px; width: 80px; color: #d1d5db; font-size: 11px; }

/* ── Restaurant Menu ─────────────────────── */
.rest-day-block { margin-bottom: 18px; }
.rest-day-header {
    background: #374151; color: white;
    font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
    padding: 6px 12px; border-radius: 6px 6px 0 0;
}
.rest-cards { display: grid; gap: 0; border: 1px solid #e5e7eb; border-radius: 0 0 8px 8px; overflow: hidden; }
.rest-card { padding: 14px 16px; background: white; border-right: 1px solid #e5e7eb; }
.rest-card:last-child { border-right: none; }
.meal-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 9px; font-weight: 800; border-radius: 4px;
    padding: 2px 7px; margin-bottom: 6px;
    text-transform: uppercase; letter-spacing: .5px;
}
.meal-tag.lunch  { background: #fef3c7; color: #b45309; border: 1px solid #f59e0b; }
.meal-tag.dinner { background: #ede9fe; color: #5b21b6; border: 1px solid #7c3aed; }
.rest-name { font-size: 13px; font-weight: 900; color: #1f2937; margin-bottom: 4px; }
.rest-menu-name { font-size: 11px; color: #4f46e5; font-weight: 700; margin-bottom: 6px; }
.rest-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
.rest-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #eef2ff; color: #4338ca; }
.rest-price { font-size: 11px; color: #6b7280; }
.rest-price strong { color: #1f2937; font-family: monospace; }
.rest-menu-details { font-size: 11px; color: #4b5563; line-height: 1.6; margin-top: 8px; border-top: 1px solid #f3f4f6; padding-top: 8px; }
.rest-tnc {
    background: #fffbeb; border: 1px solid #fde68a;
    border-radius: 8px; padding: 12px 16px; margin-top: 12px;
}
.rest-tnc-title { font-size: 10px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
.rest-tnc ul { padding-left: 16px; }
.rest-tnc li { font-size: 10.5px; color: #78350f; line-height: 1.7; margin-bottom: 2px; }

/* ── Footer ─────────────────────────────── */
.doc-footer {
    border-top: 1px solid #e5e7eb;
    padding: 12px 36px;
    display: flex; justify-content: space-between; align-items: center;
    background: #f8fafc;
}
.footer-left  { font-size: 9px; color: #9ca3af; }
.footer-right { font-size: 9px; color: #9ca3af; text-align: right; }
.footer-brand { font-weight: 800; color: #374151; }

/* ── Rate table wrap (override inline styles from prop_hotel_table) ── */
.rate-table-wrap { overflow: hidden; }
.rate-table-wrap table { width: 100% !important; border-collapse: collapse !important; font-size: 10.5px !important; table-layout: fixed !important; }
.rate-table-wrap th { padding: 7px 6px !important; font-size: 9.5px !important; min-width: unset !important; white-space: normal !important; }
.rate-table-wrap td { padding: 7px 6px !important; font-size: 10.5px !important; min-width: unset !important; white-space: normal !important; }
.rate-table-wrap th:first-child, .rate-table-wrap td:first-child { width: 22% !important; }
.rate-table-wrap th:nth-child(2), .rate-table-wrap td:nth-child(2) { width: 12% !important; }
.rate-table-wrap span[style*="inline-flex"] { display: flex !important; justify-content: space-between !important; gap: 2px !important; }

/* ── Section page break ─────────────────── */
.section {
    page-break-inside: avoid;
    break-inside: avoid;
}
.section-page {
    page-break-before: always;
    break-before: page;
    padding-top: 8px;
}
.page-break { page-break-after: always; }

/* ── Print ───────────────────────────────── */
@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
    body { background: white; }
    .toolbar { display: none !important; }
    .page-wrapper { padding: 0; background: white; }
    .page { box-shadow: none; margin: 0; width: 100%; }
    .section-page { page-break-before: always; break-before: page; }
    .section { page-break-inside: avoid; break-inside: avoid; }
    /* Force header background */
    .doc-header { background: linear-gradient(135deg,#1f2937,#374151) !important; }
    /* Force client box backgrounds */
    .client-box { background: #f9fafb !important; border: 1px solid #e5e7eb !important; }
    /* Force rate table header background */
    .rate-table-wrap thead tr { background: #1f2937!important; color: white !important; }
    /* Force inclusion/exclusion box backgrounds */
    .incl-box { background: #f0fdf4 !important; border-color: #86efac !important; }
    .excl-box { background: #fef2f2 !important; border-color: #fca5a5 !important; }
    .incl-excl-title.incl { background: #dcfce7 !important; }
    .incl-excl-title.excl { background: #fee2e2 !important; }
    /* Force itin day header */
    .itin-day-header { background: #1f2937 !important; color: white !important; }
    /* Force menu card */
    .menu-card { background: #fffbeb !important; border-color: #fde68a !important; }
    .menu-card-header { background: #fef3c7 !important; }
}
</style>
</head>
<body>

{{-- ── Toolbar ── --}}
<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ route('tour-packages.edit', $package) }}">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Edit
        </a>
        <span class="toolbar-title">{{ $package->package_code }} — Preview Proposal</span>
    </div>
    <div class="toolbar-right">
        <button class="btn-tool btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print / Save PDF
        </button>
        <a href="{{ route('tour-packages.export-word', $package) }}" class="btn-tool btn-word">
            <i class="fa-solid fa-file-word"></i> Export Word
        </a>
    </div>
</div>

<div class="page-wrapper">
<div class="page">

    {{-- ── HEADER ── --}}
    @php
        $settings        = \App\Models\Setting::allKeyed();
        $brandLogo       = !empty($settings['brand_logo']) ? '/storage/' . $settings['brand_logo'] : null;
        $companyName     = $settings['company_name']    ?? 'Diorama Destination';
        $companyTagline  = $settings['company_tagline'] ?? 'Tour & Travel Specialist';
        $footerText      = $settings['proposal_footer_text'] ?? 'Confidential';
        $companyLogo     = !empty($settings['company_logo']) ? '/storage/' . $settings['company_logo'] : null;
        $periodStr = ($package->period_from ? \Carbon\Carbon::parse($package->period_from)->format('d M Y') : '—')
                   . ($package->period_to   ? ' – ' . \Carbon\Carbon::parse($package->period_to)->format('d M Y') : '');
    @endphp
    <div class="doc-header">
        <div class="header-logo">
            @if($brandLogo)
                <img src="{{ $brandLogo }}" alt="{{ $companyName }}" style="height:48px;width:auto;object-fit:contain;">
            @else
                <div class="logo-icon"><i class="fa-solid fa-globe"></i></div>
                <div>
                    <div class="logo-text-main">{{ $companyName }}</div>
                    <div class="logo-text-sub">{{ $companyTagline }}</div>
                </div>
            @endif
        </div>
        <div class="header-right">
            <div class="header-doc-type">Proposed Program &amp; Price Quotation</div>
            <div class="header-code">{{ $package->duration }} &nbsp; {{ $package->destination }}</div>
            <div class="header-date">{{ $periodStr }}</div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="doc-body">

        {{-- ── 1. CLIENT DETAILS ── --}}
        <div class="section">
            <div class="section-title"><i class="fa-solid fa-user-tie"></i> Client Details</div>

            @php
                $customTables = is_array($package->prop_custom_tables)
                    ? $package->prop_custom_tables
                    : json_decode($package->prop_custom_tables ?? '[]', true);
                $customTables = $customTables ?: [];

                $itinBriefs = is_array($package->prop_itin_briefs)
                    ? $package->prop_itin_briefs
                    : json_decode($package->prop_itin_briefs ?? '{}', true);
                $itinBriefs = (is_array($itinBriefs) && !array_is_list($itinBriefs)) ? $itinBriefs : [];

                $menuData = is_array($package->prop_menu)
                    ? $package->prop_menu
                    : json_decode($package->prop_menu ?? '{}', true);
                $menuData = $menuData ?: [];

                $itineraryItems = $package->itinerary()->orderBy('day')->orderBy('sort_order')->get();
            @endphp

            @if($package->notes)
            <div class="pkg-name-banner">{{ $package->notes }}</div>
            @endif

            <div class="client-box">
                <div class="client-row">
                    <span class="client-label">Client / Agent</span>
                    <span class="client-value">{{ $package->agent ?: '—' }}</span>
                </div>
                <div class="client-row">
                    <span class="client-label">Destination</span>
                    <span class="client-value">{{ $package->destination ?: '—' }}</span>
                </div>
                <div class="client-row">
                    <span class="client-label">Period of Stay</span>
                    <span class="client-value">
                        {{ $package->period_from ? \Carbon\Carbon::parse($package->period_from)->format('d M Y') : '—' }}
                        @if($package->period_to) – {{ \Carbon\Carbon::parse($package->period_to)->format('d M Y') }} @endif
                    </span>
                </div>
                <div class="client-row">
                    <span class="client-label">Duration</span>
                    <span class="client-value">{{ $package->duration ?: '—' }}</span>
                </div>
                <div class="client-row">
                    <span class="client-label">Total Pax</span>
                    <span class="client-value">{{ $package->pax ?: '—' }} pax</span>
                </div>
                <div class="client-row">
                    <span class="client-label">Currency</span>
                    <span class="client-value">{{ $package->currency ?: 'IDR' }}</span>
                </div>
            </div>
        </div>

        {{-- ── 2. PACKAGE RATE ── --}}
        @if($package->prop_hotel_table)
        <div class="section">
            <div class="section-title"><i class="fa-solid fa-tags"></i> Package Rate</div>
            <div class="rate-table-wrap">
                {!! $package->prop_hotel_table !!}
            </div>
            <p class="rate-note">* The above rates quoted in {{ $package->currency }}, net per pax</p>
        </div>
        @endif

        {{-- ── 3. INCLUSION & EXCLUSION ── --}}
        @if($package->prop_inclusion || $package->prop_exclusion)
        <div class="section">
            <div class="section-title"><i class="fa-solid fa-list-check"></i> Inclusion &amp; Exclusion</div>
            <div class="incl-excl-grid">
                @if($package->prop_inclusion)
                <div class="incl-box">
                    <div class="incl-excl-title incl"><i class="fa-solid fa-circle-check"></i> Inclusion</div>
                    <div class="incl-excl-content">{!! $package->prop_inclusion !!}</div>
                </div>
                @endif
                @if($package->prop_exclusion)
                <div class="excl-box">
                    <div class="incl-excl-title excl"><i class="fa-solid fa-circle-xmark"></i> Exclusion</div>
                    <div class="incl-excl-content">{!! $package->prop_exclusion !!}</div>
                </div>
                @endif
            </div>
            @if($package->prop_tnc)
            <div style="margin-top:14px">
                <div class="incl-excl-title" style="color:#92400e;background:#fffbeb;border-color:#fde68a"><i class="fa-solid fa-file-contract"></i> Terms &amp; Conditions</div>
                <div class="incl-excl-content" style="background:#fffbeb;border-color:#fde68a">{!! $package->prop_tnc !!}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── 4. PROPOSED ITINERARY ── --}}
        @if($itineraryItems->count() > 0)
        <div class="section section-page">
            <div class="section-title"><i class="fa-solid fa-map-location-dot"></i> Proposed Itinerary</div>
            <table class="itin-table">
                <thead>
                    <tr>
                        <th style="width:70px">Day</th>
                        <th>Itinerary</th>
                        <th style="width:80px">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itineraryItems->groupBy('day') as $day => $items)
                    <tr>
                        <td class="itin-day-cell">
                            <span class="itin-day-badge">Day {{ $day }}</span>
                        </td>
                        <td class="itin-content-cell">
                            @foreach($items as $item)
                            <div class="itin-item">
                                <div class="itin-item-name">– {{ $item->item_name }}</div>
                                @if(in_array($item->item_type, ['entrance','activity']) && !empty($itinBriefs[$item->ref_id ?? '']))
                                <div class="itin-item-brief">{{ $itinBriefs[$item->ref_id] }}</div>
                                @endif
                            </div>
                            @endforeach
                        </td>
                        <td class="itin-remarks-cell"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── 5. RESTAURANT MENU ── --}}
        @if(count($menuData) > 0)
        <div class="section section-page">
            <div class="section-title"><i class="fa-solid fa-utensils"></i> Restaurant Menu</div>

            @php
                $restByDay = [];
                foreach($itineraryItems as $item) {
                    if($item->item_type === 'restaurant') {
                        $day = $item->day;
                        if(!isset($restByDay[$day])) $restByDay[$day] = [];
                        $restByDay[$day][] = $item;
                    }
                }
                ksort($restByDay);
            @endphp

            @foreach($restByDay as $day => $restItems)
            <div class="rest-day-block">
                <div class="rest-day-header">Day {{ $day }}</div>
                <div class="rest-cards" style="grid-template-columns: repeat({{ min(count($restItems), 2) }}, 1fr)">
                    @foreach($restItems as $restItem)
                    @php
                        $dayKey    = $day . ':' . $restItem->ref_id;
                        $saved     = $menuData[$dayKey] ?? $menuData[$restItem->ref_id] ?? [];
                        $mealType  = $saved['meal_type']     ?? '';
                        $menuName  = $saved['menu_name']     ?? '';
                        $serving   = $saved['serving_style'] ?? '';
                        $price     = $saved['adult_price']   ?? 0;
                        $details   = $saved['menu_details']  ?? '';
                    @endphp
                    <div class="rest-card">
                        @if($mealType)
                        <div class="meal-tag {{ $mealType }}">
                            <i class="fa-solid fa-{{ $mealType === 'lunch' ? 'sun' : 'moon' }}"></i>
                            {{ strtoupper($mealType) }}
                        </div>
                        @endif
                        <div class="rest-name">{{ $restItem->item_name }}</div>
                        @if($menuName)
                        <div class="rest-menu-name">{{ $menuName }}</div>
                        @endif
                        <div class="rest-badges">
                            @if($serving) <span class="rest-badge">{{ $serving }}</span> @endif
                        </div>
                        @if($price > 0)
                        <div class="rest-price">Meals budget: <strong>IDR {{ number_format($price) }}/pax</strong></div>
                        @endif
                        @if($details)
                        <div class="rest-menu-details">{!! $details !!}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- T&C --}}
            <div class="rest-tnc">
                <div class="rest-tnc-title"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Terms &amp; Conditions</div>
                <ul>
                    <li>Other dishes and/or beverages ordered outside from the above proposed menu will charge based on personal basis.</li>
                    <li>The above menu will subject to changes without prior notice from the restaurants.</li>
                    <li>In conditions restaurant on fully booking situation at the time required, Diorama Destination has the rights to swap the restaurant's day used or change it with other similar restaurant and/or similar meals budget.</li>
                </ul>
            </div>
        </div>
        @endif

    </div>{{-- end doc-body --}}

    {{-- ── FOOTER ── --}}
    <div class="doc-footer">
        <div class="footer-left">
            @if($companyLogo)
                <div style="display:flex;align-items:center;gap:10px;"><img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="height:40px;width:auto;object-fit:contain;"><span style="font-size:9px;color:#6b7280;">{{ $companyTagline }}</span></div>
            @else
                <div class="footer-brand">{{ $companyName }}</div>
                <div>{{ $companyTagline }}</div>
            @endif
        </div>
        <div class="footer-right">
            <div>{{ $package->package_code }}</div>
            <div>{{ $footerText }}</div>
        </div>
    </div>

</div>{{-- end .page --}}
</div>{{-- end .page-wrapper --}}

</body>
</html>