<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $form->title ?? 'Fill Form' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12">
<div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">

    <h1 class="text-2xl font-bold mb-4">{{ $form->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $form->description }}</p>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('form.response.submit') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">

        <div class="mb-4">
            <label class="block font-semibold">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Mobile Number</label>
            <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Paragraph</label>
            <textarea name="paragraph" class="w-full border p-2 rounded">{{ old('paragraph') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Short Answer</label>
            <input type="text" name="short_answer" value="{{ old('short_answer') }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold">File Upload</label>
            <input type="file" name="file_upload" class="w-full border p-2 bg-white rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Age</label>
            <input type="number" name="age" value="{{ old('age') }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Date</label>
            <input type="date" name="date_answer" value="{{ old('date_answer') }}" class="w-full border p-2 rounded">
        </div>

        <!-- Add additional fields here if needed (e.g., rating, section, text_field) -->

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 mt-4 rounded shadow-md">
            Submit
        </button>
    </form>
</div>
</body>
</html>
