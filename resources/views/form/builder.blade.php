<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create New Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-900 text-white" x-data="formBuilder()">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="w-72 p-6 backdrop-blur-md bg-white/10 border-r border-white/20 flex flex-col justify-between">
            <div>
                <div class="text-center mb-6">
                    <div class="w-24 h-24 mx-auto mb-2 rounded-full overflow-hidden border-4 border-purple-400 shadow-md">
                        <img src="https://ui-avatars.com/api/?name={{ $user->username }}&background=6b46c1&color=fff"
                            class="w-full h-full object-cover" />
                    </div>
                    <h2 class="text-2xl font-bold mt-2">{{ $user->username }}</h2>
                </div>

                <nav class="space-y-2 mt-6">
                    <a href="{{ route('form.builder') }}" class="block bg-white/10 hover:bg-white/20 px-4 py-2 rounded shadow text-center">➕ New Form</a>
                    <a href="{{ route('dashboard') }}" class="block bg-white/10 hover:bg-white/20 px-4 py-2 rounded shadow text-center">📊 Dashboard</a>
                </nav>

                <div class="mt-10">
                    <h3 class="text-sm font-semibold mb-3">🗂 Existing Forms</h3>
                    <ul class="space-y-1 text-sm text-white/80">
                        @foreach($forms as $form)
                        <li class="flex items-center gap-2 hover:text-white">
                            <span>📄 {{ $form }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white shadow-md">
                    🚪 Logout
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 overflow-y-auto">
            <div class="max-w-3xl mx-auto bg-white text-gray-900 p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Create a New Form</h2>

                @if(session('success'))
                <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
                @endif

                <input type="hidden" id="form-store-url" value="{{ route('form.store') }}">
                <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

                <form method="POST" action="{{ route('form.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="form_title" class="block text-gray-700 font-semibold">Form Title</label>
                        <input type="text" name="form_title" id="form_title" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>

                    <div id="fields-wrapper">
                        <h3 class="text-lg font-semibold mb-2">Add Fields</h3>
                        <div id="field-list"></div>
                        <button type="button" onclick="addField()" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-md">➕ Add Field</button>
                    </div>

                    <input type="hidden" name="fields" id="fields-input">

                    <button type="submit" onclick="prepareFields()" class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md">
                        ✅ Save Form
                    </button>
                </form>

                @if ($forms && count($forms))
                <hr class="my-6">
                <h3 class="text-xl font-bold mb-4">Your Forms</h3>
                <ul class="space-y-2">
                    @foreach ($forms as $form)
                    @php
                    $slug = str_replace([' ', '-'], '_', strtolower($form));
                    @endphp
                    <li class="flex justify-between items-center bg-gray-100 p-3 rounded-md">
                        <span>{{ $form }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('form.fill', ['username' => $user->username, 'slug' => $slug]) }}" class="text-blue-600 hover:underline" target="_blank">🔗 View</a>
                            <form action="{{ route('form.delete', $slug) }}" method="POST" onsubmit="return confirm('Delete this form?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">🗑️ Delete</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </main>
    </div>

    <script>
        let fields = [];

        function addField() {
            const id = Date.now();
            fields.push({
                label: '',
                type: 'text',
                id
            });

            const container = document.createElement('div');
            container.classList.add('mb-4', 'border', 'p-3', 'rounded-md', 'bg-gray-50');
            container.setAttribute('data-id', id);

            container.innerHTML = `
                <div class="mb-2">
                    <label class="block text-sm font-medium">Field Label</label>
                    <input type="text" class="w-full mt-1 p-2 border rounded" onchange="updateField(${id}, 'label', this.value)">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Field Type</label>
                    <select class="w-full mt-1 p-2 border rounded" onchange="updateField(${id}, 'type', this.value)">
                        <option value="text">Short Text</option>
                        <option value="textarea">Paragraph</option>
                        <option value="date">Date</option>
                        <option value="radio">Multiple Choice (Radio)</option>
                        <option value="checkbox">Checkboxes</option>
                    </select>
                </div>
                <button type="button" onclick="removeField(${id})" class="text-sm text-red-600 hover:underline">Remove</button>
            `;

            document.getElementById('field-list').appendChild(container);
        }

        function updateField(id, key, value) {
            fields = fields.map(f => {
                if (f.id === id) {
                    f[key] = value;
                }
                return f;
            });
        }

        function removeField(id) {
            fields = fields.filter(f => f.id !== id);
            document.querySelector(`[data-id="${id}"]`).remove();
        }

        function prepareFields() {
            const validFields = fields.filter(f => f.label.trim() !== '');
            document.getElementById('fields-input').value = JSON.stringify(validFields);
        }

        function formBuilder() {
            return {
                formTitle: '',
                formDescription: '',
                fields: [],
                addField() {
                    this.fields.push({
                        label: '',
                        type: '',
                        options: []
                    });
                },
                removeField(index) {
                    this.fields.splice(index, 1);
                },
                submitForm() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = document.getElementById('form-store-url').value;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.getElementById('csrf-token').value;
                    form.appendChild(csrf);

                    const titleInput = document.createElement('input');
                    titleInput.type = 'hidden';
                    titleInput.name = 'form_title';
                    titleInput.value = this.formTitle;
                    form.appendChild(titleInput);

                    const descInput = document.createElement('input');
                    descInput.type = 'hidden';
                    descInput.name = 'form_description';
                    descInput.value = this.formDescription;
                    form.appendChild(descInput);

                    const fieldsInput = document.createElement('input');
                    fieldsInput.type = 'hidden';
                    fieldsInput.name = 'fields';
                    fieldsInput.value = JSON.stringify(this.fields);
                    form.appendChild(fieldsInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</body>

</html>