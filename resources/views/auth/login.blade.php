<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow p-6">
        <h1 class="text-2xl font-semibold mb-4">Login</h1>
        @if (session('error'))
            <div class="mb-3 text-sm text-red-600">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-3 text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <button class="w-full py-2 rounded bg-black text-white">Masuk</button>
        </form>
    </div>
</body>
</html>
