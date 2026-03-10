@extends('layouts.app')
@section('title', 'Batch Import')
@section('content')

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded flex items-center gap-2">
    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded flex items-center gap-2">
    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fa-solid fa-file-excel mr-2 text-green-600"></i>Batch Import
    </h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    {{-- Upload Form --}}
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-upload mr-2 text-blue-600"></i>Upload Excel
        </h2>
        <form method="POST" action="{{ route('imports.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Module *</label>
                <select name="module" id="module-select" class="w-full border rounded px-3 py-2 text-sm" onchange="updateInfo(this.value)">
                    <option value="">— Pilih Module —</option>
                    <option value="hotel">Hotel Contracts</option>
                    <option value="transport">Transport Contracts</option>
                    <option value="activity">Activity Contracts</option>
                    <option value="entrance">Entrance Fee</option>
                    <option value="restaurant">Restaurant Contracts</option>
                    <option value="guide">Guide Fee</option>
                </select>
            </div>

            <div id="module-info" class="hidden mb-4 bg-yellow-50 border border-yellow-200 rounded p-3 text-xs text-yellow-800">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                <span id="module-info-text"></span>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File Excel *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv"
                       class="w-full border rounded px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700">
                <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .xls, .csv — Max 5MB</p>
            </div>

            <div class="bg-red-50 border border-red-200 rounded p-3 mb-4 text-xs text-red-700">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                <strong>Warning:</strong> Upload akan <strong>menghapus semua data lama</strong> di module yang dipilih dan menggantinya dengan data baru.
            </div>

            <button type="submit" class="w-full bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 text-sm">
                <i class="fa-solid fa-upload mr-2"></i>Upload & Import
            </button>
        </form>
    </div>

    {{-- Download Templates --}}
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
            <i class="fa-solid fa-download mr-2 text-green-600"></i>Download Template
        </h2>
        <p class="text-sm text-gray-500 mb-4">Download template Excel untuk masing-masing module. Isi data sesuai format, lalu upload.</p>
        <div class="space-y-2">
            @foreach([
                'hotel'      => ['Hotel Contracts',      'fa-hotel',        'blue'],
                'transport'  => ['Transport Contracts',   'fa-bus',          'purple'],
                'activity'   => ['Activity Contracts',    'fa-person-hiking','green'],
                'entrance'   => ['Entrance Fee',          'fa-ticket',       'yellow'],
                'restaurant' => ['Restaurant Contracts',  'fa-utensils',     'red'],
                'guide'      => ['Guide Fee',              'fa-person-chalkboard', 'indigo'],
            ] as $module => [$label, $icon, $color])
            <a href="{{ route('imports.template', $module) }}"
               class="flex items-center justify-between px-4 py-3 border rounded hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <i class="fa-solid {{ $icon }} text-{{ $color }}-600 w-4"></i>
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </div>
                <span class="text-xs text-green-600 group-hover:text-green-700">
                    <i class="fa-solid fa-download mr-1"></i>Download .xlsx
                </span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Import Logs --}}
<div class="bg-white rounded shadow p-6">
    <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2">
        <i class="fa-solid fa-clock-rotate-left mr-2 text-gray-500"></i>Import History
    </h2>
    @if($logs->isEmpty())
        <p class="text-sm text-gray-400 text-center py-4">Belum ada history import.</p>
    @else
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left font-semibold text-gray-600">Module</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-600">File</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-600">Total</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-600">Success</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-600">Failed</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-600">Status</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-600">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 capitalize font-medium">{{ $log->module }}</td>
                <td class="px-3 py-2 text-gray-600 text-xs">{{ $log->filename }}</td>
                <td class="px-3 py-2 text-center">{{ $log->total_rows }}</td>
                <td class="px-3 py-2 text-center text-green-600 font-medium">{{ $log->success_rows }}</td>
                <td class="px-3 py-2 text-center text-red-500">{{ $log->failed_rows }}</td>
                <td class="px-3 py-2">
                    @php $sc = match($log->status) { 'success'=>'bg-green-100 text-green-700','partial'=>'bg-yellow-100 text-yellow-700','failed'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' }; @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $sc }}">{{ ucfirst($log->status) }}</span>
                </td>
                <td class="px-3 py-2 text-gray-400 text-xs">{{ $log->created_at->format('d-M-Y h:i A') }}</td>
            </tr>
            @if($log->errors)
            <tr class="bg-red-50">
                <td colspan="7" class="px-3 py-2 text-xs text-red-600">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $log->errors }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
</div>

<script>
const moduleInfo = {
    hotel:      'Data hotel lama akan dihapus. Pastikan contract_code sama untuk row yang satu contract.',
    transport:  'Data transport lama akan dihapus. Kelompokkan per vendor_name untuk satu contract.',
    activity:   'Data activity lama akan dihapus. Kelompokkan per vendor_name dan activity_name.',
    entrance:   'Semua tiket entrance lama akan dihapus.',
    restaurant: 'Data restaurant lama akan dihapus. Kelompokkan per vendor_name untuk satu contract.',
    guide:      'Semua data guide fee lama akan dihapus. Satu baris = satu tier rate.',
};

function updateInfo(module) {
    const info = document.getElementById('module-info');
    const text = document.getElementById('module-info-text');
    if (module && moduleInfo[module]) {
        text.textContent = moduleInfo[module];
        info.classList.remove('hidden');
    } else {
        info.classList.add('hidden');
    }
}
</script>
@endsection
