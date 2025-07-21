<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $formTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-3xl w-full bg-white rounded shadow p-6">
            <h1 class="text-3xl font-bold mb-2">{{ $formTitle }}</h1>

            @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('form.submit', [$username, $formSlug]) }}">
                @csrf

                @foreach($fields as $field)
                <div class="mb-4">
                    <label class="block font-semibold mb-1">
                        {{ ucwords(str_replace('_', ' ', $field)) }}
                    </label>
                    <input type="text" name="{{ $field }}" class="w-full border rounded px-3 py-2" required>
                </div>
                @endforeach

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Submit Response
                </button>
            </form>

            @if(session('success'))
            <div class="mt-6 text-center space-y-4">
                <p class="text-gray-800 font-medium">Want to create your own forms?</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Register
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>

</html>
