@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage company profile and proposal defaults</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Company Profile --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-building text-blue-500"></i> Company Profile
                </h2>
            </div>
            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tagline</label>
                        <input type="text" name="company_tagline" value="{{ $settings['company_tagline'] ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Address</label>
                    <textarea name="company_address" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $settings['company_address'] ?? '' }}</textarea>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Phone</label>
                        <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email</label>
                        <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Website</label>
                        <input type="text" name="company_website" value="{{ $settings['company_website'] ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Logos --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-image text-purple-500"></i> Logos
                </h2>
            </div>
            <div class="px-6 py-6 grid grid-cols-2 gap-8">

                {{-- Brand Logo --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Brand Logo
                        <span class="text-gray-400 normal-case font-normal ml-1">(used in Word header)</span>
                    </label>
                    @if(!empty($settings['brand_logo']))
                    <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-between">
                        <img src="{{ Storage::url($settings['brand_logo']) }}" alt="Brand Logo" class="h-12 object-contain">
                        <a href="{{ route('settings.delete-logo', 'brand_logo') }}"
                            onclick="return confirm('Remove brand logo?')"
                            class="text-red-400 hover:text-red-600 text-xs ml-3">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                    @endif
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" name="brand_logo" accept="image/*" class="hidden" id="brand_logo_input"
                            onchange="previewLogo(this, 'brand_logo_preview')">
                        <label for="brand_logo_input" class="cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300 mb-2 block"></i>
                            <span class="text-sm text-gray-500">Click to upload</span>
                            <span class="text-xs text-gray-400 block mt-1">PNG, JPG, SVG — max 2MB</span>
                        </label>
                        <img id="brand_logo_preview" class="hidden mt-3 h-12 mx-auto object-contain">
                    </div>
                </div>

                {{-- Company Logo --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Company Logo
                        <span class="text-gray-400 normal-case font-normal ml-1">(used in Word footer)</span>
                    </label>
                    @if(!empty($settings['company_logo']))
                    <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-between">
                        <img src="{{ Storage::url($settings['company_logo']) }}" alt="Company Logo" class="h-12 object-contain">
                        <a href="{{ route('settings.delete-logo', 'company_logo') }}"
                            onclick="return confirm('Remove company logo?')"
                            class="text-red-400 hover:text-red-600 text-xs ml-3">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                    @endif
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                        <input type="file" name="company_logo" accept="image/*" class="hidden" id="company_logo_input"
                            onchange="previewLogo(this, 'company_logo_preview')">
                        <label for="company_logo_input" class="cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300 mb-2 block"></i>
                            <span class="text-sm text-gray-500">Click to upload</span>
                            <span class="text-xs text-gray-400 block mt-1">PNG, JPG, SVG — max 2MB</span>
                        </label>
                        <img id="company_logo_preview" class="hidden mt-3 h-12 mx-auto object-contain">
                    </div>
                </div>

            </div>
        </div>

        {{-- Proposal Defaults --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-amber-500"></i> Proposal Defaults
                </h2>
            </div>
            <div class="px-6 py-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Footer Text</label>
                    <input type="text" name="proposal_footer_text" value="{{ $settings['proposal_footer_text'] ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Confidential — For recipient use only">
                    <p class="text-xs text-gray-400 mt-1">Appears at the bottom of exported Word documents</p>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-2.5 rounded-lg text-sm transition-colors flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Save Settings
            </button>
        </div>

    </form>
</div>

<script>
function previewLogo(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection