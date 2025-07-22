<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $form->title ?? 'Form' }} - Responses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-5xl mx-auto bg-white shadow rounded px-8 py-7">
        <h1 class="text-2xl font-bold mb-6 text-blue-800">
            📋 Responses for "{{ $form->title }}"
        </h1>
        <p class="text-gray-600 mb-6">{{ $form->description }}</p>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if($responses->isEmpty())
            <div class="text-gray-500 italic mb-4 text-center">No responses have been submitted yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border bg-white shadow-sm rounded">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2 text-left text-sm font-semibold text-gray-600">#</th>
                            @foreach($form->questions as $q)
                                <th class="border-b px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                    {{ $q['label'] ?? 'Question' }}
                                </th>
                            @endforeach
                            <th class="border-b px-4 py-2 text-left text-sm font-semibold text-gray-600">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $rIndex => $response)
                            <tr class="hover:bg-blue-50">
                                <td class="border-b px-4 py-2 text-sm text-gray-500">{{ $rIndex+1 }}</td>
                                @foreach($form->questions as $qIndex => $q)
                                    <td class="border-b px-4 py-2 text-sm">
                                        @php
                                            $value = $response->response_data[$qIndex] ?? '';
                                            // Format array answers (multiple choice, checkboxes)
                                            if (is_array($value)) $value = implode(', ', $value);
                                        @endphp
                                        {{ $value ?: '—' }}
                                    </td>
                                @endforeach
                                <td class="border-b px-4 py-2 text-xs text-gray-400">
                                    {{ $response->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-8">
            <a href="{{ url()->previous() ?: route('dashboard') }}"
               class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold shadow">
                &larr; Back
            </a>
        </div>
    </div>
</body>
</html>
