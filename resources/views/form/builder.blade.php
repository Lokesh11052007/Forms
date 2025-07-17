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

        <!-- Main Content -->
        <main class="flex-1 p-10 overflow-y-auto">
            <div class="max-w-3xl mx-auto bg-white text-gray-900 p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Create a New Form</h2>

                @if(session('success'))
                <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
                @endif

                <input type="hidden" id="form-store-url" value="{{ route('form.store') }}">
                <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Form Title</label>
                        <input type="text" x-model="formTitle" required class="mt-1 block w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Form Description (optional)</label>
                        <textarea x-model="formDescription" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Add Fields</h3>

                        <template x-for="(field, index) in fields" :key="index">
                            <div class="mb-4 border p-4 rounded relative bg-gray-50">
                                <button type="button" @click="removeField(index)" class="absolute top-2 right-2 text-red-600 hover:text-red-800 text-lg">
                                    &times;
                                </button>

                                <label class="block text-sm font-medium mb-1">Question Label</label>
                                <input type="text" x-model="field.label" class="w-full border rounded p-2 mb-2" required>

                                <label class="block text-sm font-medium mb-1">Field Type</label>
                                <select x-model="field.type" class="w-full border rounded p-2 mb-2" required>
                                    <option value="">Select type</option>
                                    <option value="short">Short Answer</option>
                                    <option value="paragraph">Paragraph</option>
                                    <option value="radio">Multiple Choice (Radio)</option>
                                    <option value="checkbox">Checkboxes</option>
                                    <option value="date">Date</option>
                                </select>

                                <template x-if="field.type === 'radio' || field.type === 'checkbox'">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Options</label>
                                        <template x-for="(opt, idx) in field.options" :key="idx">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <input type="text" x-model="field.options[idx]" class="flex-1 border p-2 rounded">
                                                <button type="button" @click="field.options.splice(idx, 1)" class="text-red-600 text-sm">&#x2715;</button>
                                            </div>
                                        </template>
                                        <button type="button" @click="field.options.push('')" class="text-sm text-blue-600 hover:underline">
                                            + Add Option
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <button type="button" @click="addField" class="bg-blue-500 text-white px-4 py-2 rounded">
                            + Add Field
                        </button>
                    </div>

                    <button type="button" @click="submitForm" class="bg-green-600 text-white px-4 py-2 rounded">
                        Create Form
                    </button>

                </form>
            </div>
        </main>
    </div>

    <script>
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