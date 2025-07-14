<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $username }}'s Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Form from {{ $username }}</h2>

        @if($fields->isEmpty())
        <p>No fields available.</p>
        @else
        <form action="{{ url('/forms/' . $username) }}" method="POST">
            @csrf
            @foreach($fields as $field)
            <div class="mb-4">
                <label class="block font-semibold">{{ $field->field_name }}</label>
                <input type="text" name="answers[{{ $field->id }}]" class="w-full border p-2 rounded" required>
            </div>
            @endforeach

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
        </form>
        @endif
    </div>
</body>

</html>