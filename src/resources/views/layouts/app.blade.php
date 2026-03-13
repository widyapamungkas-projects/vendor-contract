<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-blue-800 text-white px-6 py-3 flex items-center justify-between shadow">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-file-contract text-xl"></i>
        <span class="font-bold text-lg">Vendor Contract</span>
    </div>
    <div class="flex items-center gap-4">
        {{-- Language Switch --}}
        <div class="flex gap-1 text-sm">
            <a href="{{ route('lang.switch', 'en') }}"
               class="px-2 py-1 rounded {{ app()->getLocale() == 'en' ? 'bg-white text-blue-800 font-bold' : 'hover:bg-blue-700' }}">EN</a>
            <a href="{{ route('lang.switch', 'id') }}"
               class="px-2 py-1 rounded {{ app()->getLocale() == 'id' ? 'bg-white text-blue-800 font-bold' : 'hover:bg-blue-700' }}">ID</a>
        </div>

        {{-- Bell Notification --}}
        @auth
        <div class="relative" id="bell-container">
            <button onclick="toggleBell()" class="relative p-1.5 text-white hover:text-blue-200 focus:outline-none">
                <i class="fa-solid fa-bell text-lg"></i>
                <span id="bell-badge"
                      class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-4 h-4 rounded-full flex items-center justify-center leading-none">
                    0
                </span>
            </button>
            <div id="bell-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded shadow-xl border z-50 text-gray-800">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <span class="font-semibold text-gray-700 text-sm">Payment Slip Notifications</span>
                    <button onclick="markBellRead()" class="text-xs text-blue-600 hover:text-blue-800">Mark all read</button>
                </div>
                <div id="bell-items" class="max-h-72 overflow-y-auto divide-y">
                    <div class="px-4 py-3 text-xs text-gray-400 text-center">Loading...</div>
                </div>
                <div class="px-4 py-2 border-t text-center">
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><i class="fa-solid fa-gear w-4 text-center"></i> Settings</a>
            <a href="{{ route('payment-slips.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        View all payment slips →
                    </a>
                </div>
            </div>
        </div>
        @endauth

        {{-- User Info + Logout --}}
        @auth
        <div class="flex items-center gap-3 border-l border-blue-600 pl-4">
            <div class="text-sm text-right">
                <div class="font-medium">{{ auth()->user()->name }}</div>
                <div class="text-blue-300 text-xs capitalize">{{ auth()->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-600 px-3 py-1.5 rounded text-sm flex items-center gap-1">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>

<div class="flex min-h-screen">
    <aside class="w-56 bg-white shadow-md pt-6 flex-shrink-0">
        <nav class="px-3 space-y-1">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Contracts</p>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('hotel-contracts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('hotel-contracts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-hotel w-4"></i> {{ __('contracts.title') }}
            </a>

            <a href="{{ route('transport-contracts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('transport-contracts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-bus w-4"></i> {{ __('contracts.transport_title') }}
            </a>

            <a href="{{ route('activity-contracts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('activity-contracts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-person-hiking w-4"></i> {{ __('contracts.activity_title') }}
            </a>

            <a href="{{ route('entrance-contracts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('entrance-contracts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-ticket w-4"></i> Entrance Fee
            </a>

            <a href="{{ route('restaurant-contracts.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('restaurant-contracts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-utensils w-4"></i> Restaurant
            </a>
            <a href="{{ route('guide-languages.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('guide-languages*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-person-chalkboard w-4"></i> Guide Fee
            </a>

            
            <div class="pt-3 mt-3 border-t border-gray-200 space-y-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Costing</p>
                <a href="{{ route('tour-packages.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('tour-packages.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fa-solid fa-calculator w-4"></i> Tour Costing
                </a>
            </div><div class="pt-3 mt-3 border-t space-y-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Tools</p>

                <a href="{{ route('imports.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('imports.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fa-solid fa-file-excel w-4"></i> Batch Import
                </a>

                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"><i class="fa-solid fa-gear w-4 text-center"></i> Settings</a>
            <a href="{{ route('payment-slips.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('payment-slips.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-4"></i>
                    <span>Payment Slips</span>
                    <span id="sidebar-badge" class="hidden ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">0</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1 p-6">
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
let bellOpen = false;

function toggleBell() {
    bellOpen = !bellOpen;
    document.getElementById('bell-dropdown').classList.toggle('hidden', !bellOpen);
    if (bellOpen) fetchNotifications();
}

document.addEventListener('click', function(e) {
    const container = document.getElementById('bell-container');
    if (container && !container.contains(e.target)) {
        bellOpen = false;
        document.getElementById('bell-dropdown').classList.add('hidden');
    }
});

function fetchNotifications() {
    fetch('{{ secure_url(route('payment-slips.notifications', [], false)) }}')
        .then(r => r.json())
        .then(data => {
            const badge       = document.getElementById('bell-badge');
            const sidebarBadge = document.getElementById('sidebar-badge');
            const items       = document.getElementById('bell-items');

            if (data.count > 0) {
                const label = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
                badge.textContent = label;
                sidebarBadge.classList.remove('hidden');
                sidebarBadge.textContent = label;
            } else {
                badge.classList.add('hidden');
                sidebarBadge.classList.add('hidden');
            }

            if (!items) return;

            if (data.items.length === 0) {
                items.innerHTML = '<div class="px-4 py-4 text-xs text-gray-400 text-center">No new notifications</div>';
                return;
            }

            items.innerHTML = data.items.map(item => {
                const date = item.uploaded_at ? new Date(item.uploaded_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '';
                return `
                <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer" onclick="window.location.href='{{ route('payment-slips.index') }}'">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-green-500 text-sm flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-800 truncate">${item.file_name}</div>
                            <div class="text-xs text-gray-400">${date}</div>
                        </div>
                        <span class="bg-blue-100 text-blue-600 text-xs px-1.5 py-0.5 rounded-full flex-shrink-0">New</span>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(() => {});
}

function markBellRead() {
    fetch('{{ secure_url(route("payment-slips.mark-read", [], false)) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(() => {
        document.getElementById('bell-badge').classList.add('hidden');
        document.getElementById('sidebar-badge').classList.add('hidden');
        const items = document.getElementById('bell-items');
        if (items) items.innerHTML = '<div class="px-4 py-4 text-xs text-gray-400 text-center">No new notifications</div>';
    });
}

// Poll setiap 2 menit
fetchNotifications();
setInterval(fetchNotifications, 120000);
</script>

<script>
const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
const token = document.querySelector("meta[name=csrf-token]") ? document.querySelector("meta[name=csrf-token]").content : "";
fetch("/set-timezone", {
    method: "POST",
    headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": token},
    body: JSON.stringify({timezone: tz})
});
</script>
</body>
</html>
