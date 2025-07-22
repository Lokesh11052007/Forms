<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 min-h-screen flex items-center justify-center">

    <div class="bg-white/40 backdrop-blur-lg border border-white/30 rounded-2xl shadow-2xl p-12 max-w-lg text-center flex flex-col items-center">
        <svg class="w-24 h-24 mb-6 text-red-400" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="22" fill="#F87171" fill-opacity="0.15"/>
            <path d="M16 16l16 16M32 16l-16 16" stroke="#F87171" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <h1 class="text-3xl font-bold text-red-700 mb-4">⚠️ Form Expired</h1>
        <p class="text-lg text-gray-800 mb-6">
            Sorry, this form is no longer accepting responses.<br>
            Its availability period has ended.
        </p>
        <a href="{{ url()->previous() ?: route('dashboard') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded shadow">
            &larr; Back to Dashboard
        </a>
        <div class="text-sm text-gray-600 mt-4">
            Need help? <a href="mailto:support@yourcompany.com" class="text-blue-600 hover:underline">Contact support</a>
        </div>
    </div>

</body>
</html>
