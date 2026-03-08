<table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
        <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-600">File Name</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Type</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Size</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Modified</th>
            <th class="px-4 py-3 text-center font-semibold text-gray-600">Action</th>
        </tr>
    </thead>
    <tbody class="divide-y">
        @foreach($files as $file)
        @php
            $isNew     = \App\Models\PaymentSlipNotification::where('file_id', $file->getId())->where('is_read', false)->exists();
            $ext       = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
            $iconClass = match($ext) {
                'pdf'                    => 'fa-file-pdf text-red-500',
                'xlsx','xls'             => 'fa-file-excel text-green-600',
                'docx','doc'             => 'fa-file-word text-blue-600',
                'jpg','jpeg','png','webp'=> 'fa-file-image text-purple-500',
                'zip','rar'              => 'fa-file-zipper text-orange-500',
                default                  => 'fa-file text-gray-500'
            };
            $previewable = in_array($ext, ['pdf','jpg','jpeg','png','webp']);
            $size = $file->getSize()
                ? ($file->getSize() > 1048576
                    ? round($file->getSize()/1048576,1).' MB'
                    : round($file->getSize()/1024,0).' KB')
                : '-';
            $date = $file->getModifiedTime()
                ? \Carbon\Carbon::parse($file->getModifiedTime())->setTimezone(session('user_timezone', 'Asia/Makassar'))->format('d-M-Y h:i A')
                : '-';
        @endphp
        <tr class="hover:bg-gray-50 {{ $isNew ? 'bg-blue-50' : '' }}">
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid {{ $iconClass }}"></i>
                    <span class="font-medium text-gray-800">{{ $file->getName() }}</span>
                    @if($isNew)
                    <span class="bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded-full">New</span>
                    @endif
                </div>
            </td>
            <td class="px-4 py-3 text-gray-500 uppercase text-xs hidden md:table-cell">{{ $ext ?: '-' }}</td>
            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $size }}</td>
            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $date }}</td>
            <td class="px-4 py-3 text-center">
                <div class="flex justify-center gap-2">
                    @if($previewable)
                    <button onclick="openPreview('{{ secure_url(route('payment-slips.preview', $file->getId(), false)) }}', '{{ $file->getName() }}', '{{ $ext }}')"
                            class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100">
                        <i class="fa-solid fa-eye mr-1"></i>View
                    </button>
                    @else
                    <a href="{{ route('payment-slips.download', $file->getId()) }}"
                       class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100">
                        <i class="fa-solid fa-eye mr-1"></i>View
                    </a>
                    @endif
                    <a href="{{ route('payment-slips.download', $file->getId()) }}"
                       class="px-3 py-1 bg-green-50 text-green-700 rounded text-xs hover:bg-green-100">
                        <i class="fa-solid fa-download mr-1"></i>Download
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Preview Modal --}}
@once
<div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <span id="preview-title" class="font-semibold text-gray-700 truncate max-w-lg text-sm"></span>
            <div class="flex gap-2">
                <a id="preview-download" href="#"
                   class="px-3 py-1.5 bg-green-50 text-green-700 rounded text-xs hover:bg-green-100">
                    <i class="fa-solid fa-download mr-1"></i>Download
                </a>
                <button onclick="closePreview()"
                        class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded text-xs hover:bg-gray-200">
                    <i class="fa-solid fa-xmark mr-1"></i>Close
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-hidden bg-gray-100 rounded-b-lg" id="preview-body">
            {{-- content injected by JS --}}
        </div>
    </div>
</div>

<script>
function openPreview(url, name, ext) {
    document.getElementById('preview-title').textContent = name;
    const body = document.getElementById('preview-body');
    body.innerHTML = '';

    const downloadUrl = url.replace('/preview/', '/download/');
    document.getElementById('preview-download').href = downloadUrl;

    const modal = document.getElementById('preview-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Set src AFTER modal is visible
    const imageExts = ['jpg','jpeg','png','webp'];
    if (imageExts.includes(ext)) {
        const img = document.createElement('img');
        img.src = url;
        img.className = 'max-w-full max-h-full object-contain rounded shadow';
        const wrap = document.createElement('div');
        wrap.className = 'w-full h-full flex items-center justify-center p-4';
        wrap.appendChild(img);
        body.appendChild(wrap);
    } else {
        const iframe = document.createElement('iframe');
        iframe.className = 'w-full h-full border-0';
        iframe.title = name;
        body.appendChild(iframe);
        // Set src after appended to DOM
        setTimeout(() => { iframe.src = url; }, 50);
    }
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
    document.getElementById('preview-body').innerHTML = '';
    document.body.style.overflow = '';
}

document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});
</script>
@endonce
