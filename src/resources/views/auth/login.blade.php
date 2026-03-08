<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vendor Contract</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-800 to-blue-600 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md px-4">

    {{-- LOGO --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
            <i class="fa-solid fa-file-contract text-blue-700 text-2xl"></i>
        </div>
        <h1 class="text-white text-2xl font-bold">Vendor Contract</h1>
        <p class="text-blue-200 text-sm mt-1">Diorama Travel & DMC</p>
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-gray-800 text-xl font-semibold mb-6">Sign In</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}"
                           autofocus autocomplete="email"
                           class="w-full pl-9 pr-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('email') border-red-400 @enderror"
                           placeholder="admin@vendorcontract.com">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input type="password" name="password" id="password"
                           autocomplete="current-password"
                           class="w-full pl-9 pr-10 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-blue-700 text-white py-2.5 rounded-lg font-medium hover:bg-blue-800 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i>
                Sign In
            </button>
        </form>
    </div>

    <p class="text-center text-blue-200 text-xs mt-6">
        © {{ date('Y') }} Diorama Travel & DMC
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>