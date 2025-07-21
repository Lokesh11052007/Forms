<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 min-h-screen flex items-center justify-center">

    <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl shadow-2xl p-8 w-full max-w-md text-white">
        <h2 class="text-3xl font-bold mb-6 text-center">🔐 Login to Your Account</h2>

        @if($errors->any())
        <div class="bg-red-500/20 border border-red-400 text-red-200 p-3 mb-4 rounded">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm mb-1">Username</label>
                <input type="text" name="username" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Enter your username" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 transition px-4 py-2 rounded font-semibold shadow-md">
                Login
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center text-sm text-white/80">
            Don't have an account?
            <a href="{{ route('register.form') }}" class="text-purple-300 hover:underline font-medium">Register</a>
        </div>
    </div>

</body>

</html>
