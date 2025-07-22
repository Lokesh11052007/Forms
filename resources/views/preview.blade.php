<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview My Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">My Form Preview</h2>

        @if($fields->isEmpty())
            <p class="text-gray-500">No fields added yet.</p>
        @else
            <ul class="space-y-3">
                @foreach($fields as $field)
                    <li class="border p-3 rounded break-words">
                        <strong>Field:</strong> {{ $field->field_name }}<br>
                        @if($field->url)
                            <strong>URL:</strong>
                            <a href="{{ $field->url }}" class="text-blue-500 underline break-all" target="_blank">{{ $field->url }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Optional: Back or Edit button -->
        <div class="mt-7 flex justify-between">
            <a href="{{ url()->previous() }}" class="text-blue-700 hover:underline">&larr; Back</a>
            <!-- Add button/link to the builder or form view if desired -->
        </div>
    </div>
</body>
</html>
