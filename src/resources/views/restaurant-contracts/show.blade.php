@extends('layouts.app')
@section('title', $contract->contract_code)
@section('content')

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('restaurant-contracts.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $contract->contract_code }}</h1>
        @php $sc = match($contract->status) { 'active'=>'bg-green-100 text-green-700','expiring_soon'=>'bg-yellow-100 text-yellow-700','expired'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' }; @endphp
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $sc }}">{{ ucfirst(str_replace('_',' ',$contract->status)) }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('restaurant-contracts.edit', $contract) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 flex items-center gap-2">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <form method="POST" action="{{ route('restaurant-contracts.destroy', $contract) }}" onsubmit="return confirm('Hapus contract ini?')">
            @csrf @method('DELETE')
            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Info Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fa-solid fa-utensils mr-2 text-blue-600"></i>Restaurant Info</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Restaurant</dt><dd class="font-medium">{{ $contract->vendor_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">City</dt><dd>{{ $contract->vendor_city }}, {{ $contract->vendor_country }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Category</dt><dd>{{ $contract->price_category }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Currency</dt><dd>{{ $contract->currency }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Valid From</dt><dd>{{ $contract->valid_from->format('d-M-Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Valid Until</dt><dd>{{ $contract->valid_until->format('d-M-Y') }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fa-solid fa-user mr-2 text-blue-600"></i>PIC Sales</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Name</dt><dd class="font-medium">{{ $contract->pic_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $contract->pic_phone ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $contract->pic_email ?? '-' }}</dd></div>
            @if($contract->notes)<div class="pt-2 border-t"><dt class="text-gray-500 mb-1">Notes</dt><dd>{{ $contract->notes }}</dd></div>@endif
        </dl>
    </div>
</div>

{{-- Menus Accordion --}}
<div class="space-y-3">
@foreach($contract->menus as $index => $menu)
@php
    $styleLabel = match($menu->serving_style) { 'set_menu'=>'Set Menu','family_set'=>'Family Set','buffet'=>'Buffet' };
    $styleColor = match($menu->serving_style) { 'set_menu'=>'bg-blue-100 text-blue-700','family_set'=>'bg-purple-100 text-purple-700','buffet'=>'bg-orange-100 text-orange-700' };
    $adultFormatted = $contract->currency . ' ' . number_format($menu->adult_price, 0, ',', '.');
    $childFormatted = $contract->currency . ' ' . number_format($menu->child_price, 0, ',', '.');
@endphp
<div class="bg-white rounded shadow overflow-hidden">
    {{-- Accordion Header --}}
    <div class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-gray-50 transition"
         onclick="toggleAccordion({{ $index }})">
        <div class="flex items-center gap-3">
            <i id="accordion-icon-{{ $index }}" class="fa-solid fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
            <span class="px-2 py-1 rounded text-xs font-medium {{ $styleColor }}">{{ $styleLabel }}</span>
            <h2 class="font-semibold text-gray-700">{{ $menu->menu_name }}</h2>
            @if($menu->min_pax)
                <span class="text-sm text-gray-400"><i class="fa-solid fa-users mr-1"></i>Min {{ $menu->min_pax }} pax</span>
            @endif
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right text-sm">
                <div class="text-xs text-gray-400">Adult</div>
                <div class="font-bold text-green-700">{{ $adultFormatted }}</div>
            </div>
            <div class="text-right text-sm">
                <div class="text-xs text-gray-400">Child (65%)</div>
                <div class="font-semibold text-blue-600">{{ $childFormatted }}</div>
            </div>
            {{-- Action buttons - stop propagation so accordion doesn't toggle --}}
            <div class="flex gap-2 ml-2" onclick="event.stopPropagation()">
                <button onclick="copyRichText({{ $index }})"
                        class="flex items-center gap-1 px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                    <i class="fa-solid fa-copy"></i> Copy
                </button>
                <button onclick="toggleAccordion({{ $index }})"
                        class="flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition">
                    <i class="fa-solid fa-eye" id="view-icon-{{ $index }}"></i>
                    <span id="view-label-{{ $index }}">View Menu</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Accordion Body --}}
    <div id="accordion-body-{{ $index }}" class="hidden border-t">
        <div class="px-6 py-4">
            {{-- Menu Details --}}
            @if($menu->menu_details)
            <div id="menu-content-{{ $index }}" class="prose prose-sm max-w-none text-gray-700 mb-4">
                {!! $menu->menu_details !!}
            </div>
            @else
            <p class="text-gray-400 text-sm italic">No menu details.</p>
            @endif

            @if($menu->notes)
            <p class="text-xs text-gray-500 border-t pt-3 mt-3">
                <i class="fa-solid fa-circle-info mr-1"></i>{{ $menu->notes }}
            </p>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>

{{-- Hidden copy template --}}
@foreach($contract->menus as $index => $menu)
@php
    $styleLabel = match($menu->serving_style) { 'set_menu'=>'Set Menu','family_set'=>'Family Set','buffet'=>'Buffet' };
    $adultFormatted = $contract->currency . ' ' . number_format($menu->adult_price, 0, ',', '.');
    $childFormatted = $contract->currency . ' ' . number_format($menu->child_price, 0, ',', '.');
@endphp
<div id="copy-source-{{ $index }}" style="position:absolute;left:-9999px;top:-9999px;">
    <div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;">
        <p style="margin:0 0 4px 0;"><strong>{{ $menu->menu_name }}</strong> &nbsp;<em style="color:#666;">({{ $styleLabel }})</em></p>
        <p style="margin:0 0 8px 0; color:#333;">
            Adult: <strong style="color:#16a34a;">{{ $adultFormatted }}</strong> &nbsp;|&nbsp;
            Child: <strong style="color:#2563eb;">{{ $childFormatted }}</strong>
            @if($menu->min_pax) &nbsp;|&nbsp; Min {{ $menu->min_pax }} pax @endif
        </p>
        <hr style="border:none;border-top:1px solid #eee;margin:8px 0;">
        {!! $menu->menu_details !!}
        @if($menu->notes)
        <p style="margin:8px 0 0 0; color:#888; font-size:12px;"><em>Note: {{ $menu->notes }}</em></p>
        @endif
    </div>
</div>
@endforeach

{{-- Toast --}}
<div id="toast" class="hidden fixed bottom-6 right-6 bg-gray-800 text-white px-4 py-2 rounded shadow-lg text-sm z-50 flex items-center gap-2">
    <i class="fa-solid fa-check"></i><span id="toast-msg">Copied!</span>
</div>

<script>
function toggleAccordion(index) {
    const body = document.getElementById('accordion-body-' + index);
    const icon = document.getElementById('accordion-icon-' + index);
    const viewLabel = document.getElementById('view-label-' + index);
    const viewIcon = document.getElementById('view-icon-' + index);
    const isOpen = !body.classList.contains('hidden');

    if (isOpen) {
        body.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
        viewLabel.textContent = 'View Menu';
        viewIcon.className = 'fa-solid fa-eye';
    } else {
        body.classList.remove('hidden');
        icon.style.transform = 'rotate(90deg)';
        viewLabel.textContent = 'Hide Menu';
        viewIcon.className = 'fa-solid fa-eye-slash';
    }
}

function copyRichText(index) {
    const source = document.getElementById('copy-source-' + index);

    try {
        // Modern clipboard API with HTML support
        const htmlContent = source.innerHTML;
        const textContent = source.innerText;

        if (window.ClipboardItem) {
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const textBlob = new Blob([textContent], { type: 'text/plain' });
            navigator.clipboard.write([
                new ClipboardItem({ 'text/html': blob, 'text/plain': textBlob })
            ]).then(() => showToast('Copied with formatting!'))
              .catch(() => fallbackCopy(source));
        } else {
            fallbackCopy(source);
        }
    } catch(e) {
        fallbackCopy(source);
    }
}

function fallbackCopy(source) {
    // execCommand approach — preserves rich text when pasting into Word/Docs
    const range = document.createRange();
    range.selectNodeContents(source);
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
    document.execCommand('copy');
    sel.removeAllRanges();
    showToast('Copied! (paste into Word/Docs)');
}

function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 2500);
}
</script>
@endsection
