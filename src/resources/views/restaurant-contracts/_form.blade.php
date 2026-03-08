@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
    <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Quill CDN --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

{{-- Basic Info --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2"><i class="fa-solid fa-circle-info mr-2 text-blue-600"></i>Basic Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name *</label>
            <input type="text" name="contract[vendor_name]" value="{{ old('contract.vendor_name', $contract->vendor_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
            <input type="text" name="contract[vendor_city]" value="{{ old('contract.vendor_city', $contract->vendor_city ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <input type="text" name="contract[vendor_country]" value="{{ old('contract.vendor_country', $contract->vendor_country ?? 'Indonesia') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PIC Name *</label>
            <input type="text" name="contract[pic_name]" value="{{ old('contract.pic_name', $contract->pic_name ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PIC Phone</label>
            <input type="text" name="contract[pic_phone]" value="{{ old('contract.pic_phone', $contract->pic_phone ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PIC Email</label>
            <input type="email" name="contract[pic_email]" value="{{ old('contract.pic_email', $contract->pic_email ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price Category *</label>
            <select name="contract[price_category]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="FIT" {{ old('contract.price_category', $contract->price_category ?? 'FIT')=='FIT'?'selected':'' }}>FIT</option>
                <option value="GIT" {{ old('contract.price_category', $contract->price_category ?? '')=='GIT'?'selected':'' }}>GIT</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
            <select name="contract[currency]" class="w-full border rounded px-3 py-2 text-sm">
                <option value="IDR" {{ old('contract.currency', $contract->currency ?? 'IDR')=='IDR'?'selected':'' }}>IDR</option>
                <option value="USD" {{ old('contract.currency', $contract->currency ?? '')=='USD'?'selected':'' }}>USD</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Valid From *</label>
            <input type="date" name="contract[valid_from]"
                   value="{{ old('contract.valid_from', isset($contract->valid_from) ? $contract->valid_from->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until *</label>
            <input type="date" name="contract[valid_until]"
                   value="{{ old('contract.valid_until', isset($contract->valid_until) ? $contract->valid_until->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="contract[notes]" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('contract.notes', $contract->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Menus --}}
<div class="bg-white rounded shadow p-6 mb-4">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="font-semibold text-gray-700"><i class="fa-solid fa-book-open mr-2 text-blue-600"></i>Menus</h2>
        <button type="button" onclick="addMenu()"
                class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded hover:bg-blue-100">
            <i class="fa-solid fa-plus mr-1"></i>Add Menu
        </button>
    </div>

    @php
        $menus = old('menus', isset($contract) ? $contract->menus->toArray() : [[]]);
    @endphp

    <div id="menus-container" class="space-y-6">
        @foreach($menus as $mi => $menu)
        <div class="menu-block border-2 border-orange-100 rounded-lg p-4 bg-orange-50/20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Menu Name *</label>
                    <input type="text" name="menus[{{ $mi }}][menu_name]"
                           value="{{ $menu['menu_name'] ?? '' }}"
                           placeholder="e.g. Nasi Campur Special"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Serving Style *</label>
                    <select name="menus[{{ $mi }}][serving_style]"
                            class="w-full border rounded px-2 py-1.5 text-sm bg-white"
                            onchange="toggleMinPax(this)">
                        <option value="set_menu"   {{ ($menu['serving_style'] ?? 'set_menu')=='set_menu'  ?'selected':'' }}>Set Menu (Per Pax)</option>
                        <option value="family_set" {{ ($menu['serving_style'] ?? '')=='family_set'?'selected':'' }}>Family Set Menu</option>
                        <option value="buffet"     {{ ($menu['serving_style'] ?? '')=='buffet'    ?'selected':'' }}>Buffet</option>
                    </select>
                </div>
                <div class="min-pax-field" style="{{ in_array($menu['serving_style'] ?? 'set_menu', ['family_set','buffet']) ? '' : 'display:none' }}">
                    <label class="block text-xs text-gray-500 mb-1">Min Pax</label>
                    <input type="number" name="menus[{{ $mi }}][min_pax]"
                           value="{{ $menu['min_pax'] ?? '' }}" min="1"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
            </div>

            {{-- Harga --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <label class="block text-xs font-semibold text-blue-700 mb-1">
                        <i class="fa-solid fa-person mr-1"></i>Adult Price *
                    </label>
                    <input type="number" name="menus[{{ $mi }}][adult_price]"
                           value="{{ $menu['adult_price'] ?? '' }}" step="1000"
                           placeholder="0"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white adult-price-input"
                           oninput="updateChildPrice(this)">
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <label class="block text-xs font-semibold text-green-700 mb-1">
                        <i class="fa-solid fa-child mr-1"></i>Child Price <span class="font-normal text-gray-400">(auto 65%)</span>
                    </label>
                    <input type="text" readonly
                           value="{{ isset($menu['adult_price']) ? number_format($menu['adult_price'] * 0.65, 0, ',', '.') : '' }}"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-gray-100 text-gray-600 child-price-display">
                </div>
            </div>

            {{-- Menu Details (Quill) --}}
            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Menu Details</label>
                <div id="quill-editor-{{ $mi }}" class="bg-white min-h-32" style="border-radius: 0 0 4px 4px;">{!! $menu['menu_details'] ?? '' !!}</div>
                <input type="hidden" name="menus[{{ $mi }}][menu_details]" id="menu-details-{{ $mi }}" value="{{ $menu['menu_details'] ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Notes</label>
                <input type="text" name="menus[{{ $mi }}][notes]"
                       value="{{ $menu['notes'] ?? '' }}"
                       placeholder="e.g. Minimum order 2 jam sebelum kedatangan"
                       class="w-full border rounded px-2 py-1.5 text-sm bg-white">
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="this.closest('.menu-block').remove()"
                        class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100">
                    <i class="fa-solid fa-trash mr-1"></i>Remove Menu
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
let menuIndex = {{ count($menus) }};
const quillInstances = {};

// Init quill for existing menus
document.addEventListener('DOMContentLoaded', function() {
    @foreach($menus as $mi => $menu)
    initQuill({{ $mi }});
    @endforeach
});

function initQuill(mi) {
    const editor = document.getElementById('quill-editor-' + mi);
    if (!editor) return;
    const quill = new Quill('#quill-editor-' + mi, {
        theme: 'snow',
        placeholder: 'Tulis detail menu di sini...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });
    quillInstances[mi] = quill;
    quill.on('text-change', function() {
        document.getElementById('menu-details-' + mi).value = quill.root.innerHTML;
    });
}

function addMenu() {
    const mi = menuIndex++;
    document.getElementById('menus-container').insertAdjacentHTML('beforeend', `
        <div class="menu-block border-2 border-orange-100 rounded-lg p-4 bg-orange-50/20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Menu Name *</label>
                    <input type="text" name="menus[${mi}][menu_name]" placeholder="e.g. Nasi Campur Special"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Serving Style *</label>
                    <select name="menus[${mi}][serving_style]" class="w-full border rounded px-2 py-1.5 text-sm bg-white"
                            onchange="toggleMinPax(this)">
                        <option value="set_menu">Set Menu (Per Pax)</option>
                        <option value="family_set">Family Set Menu</option>
                        <option value="buffet">Buffet</option>
                    </select>
                </div>
                <div class="min-pax-field" style="display:none">
                    <label class="block text-xs text-gray-500 mb-1">Min Pax</label>
                    <input type="number" name="menus[${mi}][min_pax]" min="1"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <label class="block text-xs font-semibold text-blue-700 mb-1">
                        <i class="fa-solid fa-person mr-1"></i>Adult Price *
                    </label>
                    <input type="number" name="menus[${mi}][adult_price]" step="1000" placeholder="0"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-white adult-price-input"
                           oninput="updateChildPrice(this)">
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <label class="block text-xs font-semibold text-green-700 mb-1">
                        <i class="fa-solid fa-child mr-1"></i>Child Price <span class="font-normal text-gray-400">(auto 65%)</span>
                    </label>
                    <input type="text" readonly placeholder="Auto"
                           class="w-full border rounded px-2 py-1.5 text-sm bg-gray-100 text-gray-600 child-price-display">
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Menu Details</label>
                <div id="quill-editor-${mi}" class="bg-white min-h-32"></div>
                <input type="hidden" name="menus[${mi}][menu_details]" id="menu-details-${mi}">
            </div>
            <div class="mb-3">
                <label class="block text-xs text-gray-500 mb-1">Notes</label>
                <input type="text" name="menus[${mi}][notes]" placeholder="e.g. Minimum order 2 jam sebelum kedatangan"
                       class="w-full border rounded px-2 py-1.5 text-sm bg-white">
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="this.closest('.menu-block').remove()"
                        class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100">
                    <i class="fa-solid fa-trash mr-1"></i>Remove Menu
                </button>
            </div>
        </div>`);
    initQuill(mi);
}

function toggleMinPax(select) {
    const field = select.closest('.menu-block').querySelector('.min-pax-field');
    field.style.display = ['family_set','buffet'].includes(select.value) ? '' : 'none';
}

function updateChildPrice(input) {
    const block = input.closest('.menu-block');
    const childDisplay = block.querySelector('.child-price-display');
    const val = parseFloat(input.value) || 0;
    const child = Math.round(val * 0.65);
    childDisplay.value = child > 0 ? child.toLocaleString('id-ID') : '';
}

// Save all quill content before submit
document.addEventListener('submit', function() {
    Object.keys(quillInstances).forEach(mi => {
        const el = document.getElementById('menu-details-' + mi);
        if (el) el.value = quillInstances[mi].root.innerHTML;
    });
});
</script>
