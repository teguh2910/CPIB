<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pemberitahuan Impor')</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="min-h-screen bg-gray-50">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="font-semibold"><a href="{{ route('import.index') }}" class="text-sm underline">Pemberitahuan Impor Barang</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('parties.index') }}" class="text-sm underline">Master Supplier</a>
                <span class="text-sm text-gray-500">Hi, {{ session('user_name') }}</span>
                @if(session('token_expires_at'))
                    <span class="text-sm text-orange-600" id="token-countdown">
                        Token expires in: <span id="countdown-timer">--:--:--</span>
                    </span>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm px-3 py-1 rounded border">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto p-4">
        @if (session('success'))
            <div class="mb-3 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
    <script>
        @if(session('token_expires_at'))
            const tokenExpiresAt = {{ session('token_expires_at') }} * 1000; // Convert to milliseconds
            
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = tokenExpiresAt - now;
                
                if (timeLeft <= 0) {
                    document.getElementById('countdown-timer').innerHTML = 'EXPIRED';
                    document.getElementById('token-countdown').classList.remove('text-orange-600');
                    document.getElementById('token-countdown').classList.add('text-red-600');
                    return;
                }
                
                const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                document.getElementById('countdown-timer').innerHTML = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
                
                // Change color when less than 5 minutes
                if (timeLeft < 5 * 60 * 1000) {
                    document.getElementById('token-countdown').classList.remove('text-orange-600');
                    document.getElementById('token-countdown').classList.add('text-red-600');
                }
            }
            
            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        @endif
    </script>
</body>

</html>
