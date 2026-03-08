@extends('layouts.app')
@section('title', __('contracts.edit'))

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('hotel-contracts.show', $contract) }}" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">{{ __('contracts.edit') }} - {{ $contract->contract_code }}</h1>
</div>

<form method="POST" action="{{ route('hotel-contracts.update', $contract) }}">
    @csrf @method('PUT')
    @include('hotel-contracts._form')
    <div class="flex gap-3 mt-6">
        <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800">
            <i class="fa-solid fa-save mr-2"></i>{{ __('contracts.save') }}
        </button>
        <a href="{{ route('hotel-contracts.show', $contract) }}" class="px-6 py-2 rounded border hover:bg-gray-50">
            {{ __('contracts.cancel') }}
        </a>
    </div>
</form>
@endsection
