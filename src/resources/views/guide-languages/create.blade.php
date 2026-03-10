@extends('layouts.app')
@section('title', 'Add Guide Language')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-5">
        <a href="{{ route('guide-languages.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Guide Fee</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-1">Add Guide Language</h2>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('guide-languages.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Language Name</label>
                <input type="text" name="language_name"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('language_name') border-red-400 @enderror"
                    value="{{ old('language_name') }}" placeholder="e.g. English, Mandarin" required>
                @error('language_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Destination</label>
                <input type="text" name="destination" list="dest-list"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ old('destination') }}" placeholder="e.g. Bali, Lombok">
                <datalist id="dest-list">
                    @foreach($destinations as $d)<option value="{{ $d }}">@endforeach
                </datalist>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Currency</label>
                <select name="currency" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @foreach(['IDR','USD','SGD','MYR','EUR'] as $c)
                        <option value="{{ $c }}" {{ old('currency','IDR')===$c?'selected':'' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded text-sm font-semibold">Save</button>
                <a href="{{ route('guide-languages.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2 rounded text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
