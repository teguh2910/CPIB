<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
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
            <div class="font-semibold"><a href="{{ route('import.index') }}" class="text-sm underline">Import App</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('parties.index') }}" class="text-sm underline">Master Supplier</a>
                <span class="text-sm text-gray-500">Hi, {{ session('user_name') }}</span>
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
</body>

</html>
