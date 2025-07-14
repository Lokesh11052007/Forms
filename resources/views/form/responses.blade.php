<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Responses - {{ $formName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#0f0f0f] text-white min-h-screen p-10">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Responses for "{{ $formName }}"</h1>

        @if($responses->isEmpty())
        <p class="text-gray-400">No responses found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-[#1c1c1c] text-sm rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-[#2a2a2a] text-left">
                        @foreach((array) $responses->first() as $key => $value)
                        <th class="px-4 py-2 border-b border-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($responses as $row)
                    <tr class="hover:bg-[#292929]">
                        @foreach((array) $row as $value)
                        <td class="px-4 py-2 border-b border-gray-800">{{ $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="text-purple-400 hover:underline">← Back to Dashboard</a>
        </div>
    </div>
</body>

</html>