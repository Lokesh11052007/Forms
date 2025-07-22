<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 min-h-screen flex items-center justify-center">

    <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl shadow-2xl p-8 w-full max-w-md text-white">
        <h2 class="text-3xl font-bold mb-6 text-center">🔐 Login to Your Account</h2>

        @if($errors->any())
            <div class="bg-red-500/20 border border-red-400 text-red-200 p-3 mb-4 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-400 text-green-200 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5" autocomplete="off">
            @csrf
            <div>
                <label class="block text-sm mb-1" for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" autocomplete="username"
                    class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300"
                    placeholder="Enter your username" required autofocus>
            </div>

            <div>
                <label class="block text-sm mb-1" for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password"
                    class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300"
                    placeholder="Enter your password" required>
            </div>

            <!-- Remember me checkbox (optional):
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="remember" id="remember" class="accent-purple-500" />
                <label for="remember" class="text-sm">Remember me</label>
            </div>
            -->

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 transition px-4 py-2 rounded font-semibold shadow-md">
                Login
            </button>
        </form>

        <!-- Google Login Button (if using social login) -->
        <div class="my-7">
            <a href="{{ route('auth.google.redirect') }}"
                class="w-full flex items-center justify-center bg-white text-gray-800 py-2 rounded hover:bg-gray-200 transition font-semibold shadow-md">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 mr-2" alt="Google Logo">
                Login with Google
            </a>
        </div>

        <div class="mt-6 text-center text-sm text-white/80">
            Don't have an account?
            <a href="{{ route('register.form') }}" class="text-purple-300 hover:underline font-medium">Register</a>
        </div>
    </div>
</body>
</html>
