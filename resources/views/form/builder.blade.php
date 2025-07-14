<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Add Field to Your Form</h2>

        @if(session('success'))
        <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
        @endif

        <form action="{{ route('form.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Field Name</label>
                <input type="text" name="field_name" class="mt-1 block w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Optional URL</label>
                <input type="url" name="url" class="mt-1 block w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Field</button>
        </form>
    </div>
</body>

</html>