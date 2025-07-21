<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 min-h-screen flex items-center justify-center">

    <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-xl shadow-2xl p-8 w-full max-w-md text-white">
        <h2 class="text-3xl font-bold mb-6 text-center">📝 Create a New Account</h2>

        @if($errors->any())
        <div class="bg-red-500/20 border border-red-400 text-red-200 p-3 mb-4 rounded">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm mb-1">Username</label>
                <input type="text" name="username" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Choose a username" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Enter your email" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Create a password" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full p-3 rounded bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 transition px-4 py-2 rounded font-semibold shadow-md">
                Register
            </button>
        </form>

        <!-- Social Login Divider -->
        <div class="my-6 border-t border-white/30 text-center text-sm text-white/60 relative">
            <span class="bg-white/10 px-3 absolute -top-3 left-1/2 transform -translate-x-1/2">or register with</span>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google.redirect') }}" class="w-full flex items-center justify-center bg-white text-gray-800 py-2 rounded hover:bg-gray-200 transition font-semibold shadow-md">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 mr-2" alt="Google Logo">
            Sign up with Google
        </a>

        <div class="mt-6 text-center text-sm text-white/80">
            Already have an account?
            <a href="{{ route('login.form') }}" class="text-purple-300 hover:underline font-medium">Login here</a>
        </div>
    </div>

</body>

</html>
