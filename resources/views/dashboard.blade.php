<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 min-h-screen flex text-white">

    <!-- Sidebar -->
    <aside class="w-72 p-6 backdrop-blur-md bg-white/10 border-r border-white/20 flex flex-col justify-between">
        <div>
            <div class="text-center mb-6">
                <div class="w-24 h-24 mx-auto mb-2 rounded-full overflow-hidden border-4 border-purple-400 shadow-md">
                    <img src="https://ui-avatars.com/api/?name={{ $user->username }}&background=6b46c1&color=fff" class="w-full h-full object-cover" />
                </div>
                <h2 class="text-2xl font-bold mt-2">{{ $user->username }}</h2>
            </div>

            <nav class="space-y-2 mt-6">
                <a href="#" class="block bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded shadow-md text-center">📋 Existing</a>
                <a href="{{ route('form.builder') }}" class="block bg-white/10 hover:bg-white/20 px-4 py-2 rounded shadow text-center">➕ New Form</a>
            </nav>

            <div class="mt-10">
                <h3 class="text-sm font-semibold mb-3">🗂 Existing Forms</h3>
                <ul class="space-y-1 text-sm text-white/80">
                    @foreach($forms as $form)
                    <li class="flex items-center gap-2 hover:text-white">
                        <span>📄</span>{{ $form }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white shadow-md">
                🚪 Logout
            </button>
        </form>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">📊 Dashboard Overview</h1>
            <a href="{{ route('form.builder') }}" class="bg-purple-600 hover:bg-purple-700 transition px-4 py-2 rounded shadow-md text-white">
                + New Form
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($responses as $item)
            <a href="{{ route('form.responses', ['form' => $item['name']]) }}"
                class="block backdrop-blur-md bg-white/10 hover:bg-white/20 transition p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold mb-1">{{ $item['name'] }}</h2>
                <p class="text-white/70">Responses: {{ $item['count'] }}</p>
            </a>
            @endforeach
        </div>
    </main>

</body>

</html>