@extends('layouts.app')
@section('title', 'Edit Entrance Ticket')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('entrance-contracts.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Edit — {{ $ticket->attraction_name }}</h1>
</div>
<form method="POST" action="{{ route('entrance-contracts.update', $ticket) }}">
    @csrf @method('PUT')
    @include('entrance-contracts._form', ['ticket' => $ticket])
    <div class="flex gap-3 mt-6">
        <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800">
            <i class="fa-solid fa-save mr-2"></i>Save
        </button>
        <a href="{{ route('entrance-contracts.index') }}" class="px-6 py-2 rounded border hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
