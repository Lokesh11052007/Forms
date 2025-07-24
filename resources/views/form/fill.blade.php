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
                        {{ ucwords(str_replace('_', ' ', $field['name'])) }}
                    </label>

                    @if($field['type'] === 'textarea')
                    <textarea name="{{ $field['name'] }}" class="w-full border rounded px-3 py-2" rows="4" required></textarea>

                    @elseif($field['type'] === 'date')
                    <input type="date" name="{{ $field['name'] }}" class="w-full border rounded px-3 py-2" required>

                    @elseif($field['type'] === 'radio' && !empty($field['options']))
                    <div class="space-y-2">
                        @foreach(explode(',', $field['options']) as $option)
                        <div class="flex items-center">
                            <input type="radio" name="{{ $field['name'] }}" id="{{ $field['name'] }}_{{ $loop->index }}"
                                value="{{ trim($option) }}" class="mr-2" {{ $loop->first ? 'required' : '' }}>
                            <label for="{{ $field['name'] }}_{{ $loop->index }}">{{ trim($option) }}</label>
                        </div>
                        @endforeach
                    </div>

                    @elseif($field['type'] === 'checkbox' && !empty($field['options']))
                    <div class="space-y-2">
                        @foreach(explode(',', $field['options']) as $option)
                        <div class="flex items-center">
                            <input type="checkbox" name="{{ $field['name'] }}[]" id="{{ $field['name'] }}_{{ $loop->index }}"
                                value="{{ trim($option) }}" class="mr-2">
                            <label for="{{ $field['name'] }}_{{ $loop->index }}">{{ trim($option) }}</label>
                        </div>
                        @endforeach
                    </div>

                    @elseif($field['type'] === 'number')
                    <input type="number" name="{{ $field['name'] }}" class="w-full border rounded px-3 py-2" required>

                    @elseif($field['type'] === 'email')
                    <input type="email" name="{{ $field['name'] }}" class="w-full border rounded px-3 py-2" required>

                    @else
                    <input type="text" name="{{ $field['name'] }}" class="w-full border rounded px-3 py-2" required>
                    @endif
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