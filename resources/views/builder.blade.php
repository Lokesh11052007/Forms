<!DOCTYPE html>
<html lang="en" x-data="formBuilder()" x-init="init()" x-cloak>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Builder</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<main class="flex-1 flex items-center justify-center">
  <div class="max-w-3xl w-full mx-auto bg-white p-0 rounded shadow mt-8 flex flex-col">

    <!-- Editable Header -->
    <header class="bg-white rounded-t px-6 py-4 flex items-center justify-between border-b">
      <div class="flex items-center space-x-3">
        <label class="cursor-pointer">
          <input type="file" accept="image/*" class="hidden" @change="onLogoChange">
          <img :src="header.logoUrl" alt="Company Logo" class="h-10 w-10 rounded-full object-cover border inline-block"  />
          <span class="absolute left-0 top-10 text-xs text-blue-400 hover:underline">Edit</span>
        </label>
        <input type="text" x-model="header.companyName" class="text-lg font-bold text-gray-700 border-b focus:outline-none focus:ring px-1 py-0.5 w-40" />
      </div>
      <div class="relative" x-data="{ open: false }" @click.outside="open=false">
        <button @click="open=!open" class="flex items-center space-x-2 focus:ring px-2 py-1 rounded hover:bg-gray-100">
          <img src="https://ui-avatars.com/api/?name=" :alt="header.profileName" class="h-8 w-8 rounded-full border" />
          <input type="text" x-model="header.profileName"
            class="hidden sm:inline text-gray-700 font-medium border-b border-transparent focus:border-blue-400 focus:outline-none px-0.5 w-24 bg-transparent"
            aria-label="Profile Name" />
          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.085l3.71-3.855a.75.75 0 111.08 1.04l-4.24 4.4a.75.75 0 01-1.08 0l-4.24-4.4a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>
        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50">
          <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-600">My Account</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-600">Settings</a>
          <div class="border-t my-1"></div>
          <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-red-500">Logout</a>
        </div>
      </div>
    </header>
    <!-- /Header -->

    <!-- FORM BODY -->
    <div class="px-6 pt-4 pb-0 flex-1">
      <label class="sr-only" for="form-title">Form Title</label>
      <input id="form-title" x-model="form.title" placeholder="Form Title"
        class="text-xl font-semibold w-full border-b-2 border-gray-300 focus:outline-none focus:border-blue-500 mb-4"
        aria-label="Form Title" />
      <label class="sr-only" for="form-description">Form Description</label>
      <textarea id="form-description" x-model="form.description" placeholder="Form Description"
        class="w-full border p-2 rounded mb-4"
        aria-label="Form Description"></textarea>

      <div class="relative mb-6" @click.outside="addFieldDropdown = false">
        <button type="button"
          @click="addFieldDropdown = !addFieldDropdown"
          class="flex items-center justify-between bg-blue-600 text-white px-4 py-2 rounded w-56 hover:bg-blue-700 focus:ring-2 focus:ring-blue-400"
          aria-haspopup="listbox" :aria-expanded="addFieldDropdown.toString()">
          + Add Field
          <svg class="w-4 h-4 ml-2 transform transition-transform"
            :class="{ 'rotate-180': addFieldDropdown }"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <ul x-show="addFieldDropdown" x-transition
          class="absolute mt-2 w-64 bg-white border border-gray-300 rounded shadow-lg z-50 overflow-hidden"
          role="listbox"
          @keydown.escape.window="addFieldDropdown = false">
          <template x-for="type in fieldTypes" :key="type.value">
            <li>
              <button
                @click="addField(type.value)"
                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2 focus:bg-blue-100 outline-none"
                :aria-label="`Add ${type.label}`"
                role="option">
                <span x-text="type.emoji"></span>
                <span x-text="type.label"></span>
              </button>
            </li>
          </template>
        </ul>
      </div>

      <template x-for="(q, i) in form.questions" :key="i">
        <div class="mb-4 border p-4 rounded bg-gray-50 relative group">
          <div class="absolute top-2 right-8 flex space-x-2 p-2 mt-1">
            <button @click="moveQuestionUp(i)" :disabled="i === 0"
              class="text-blue-600 hover:text-blue-800 disabled:opacity-30 transition" title="Move Up" aria-label="Move Question Up">
              &#9650;
            </button>
            <button @click="moveQuestionDown(i)" :disabled="i === form.questions.length - 1"
              class="text-blue-600 hover:text-blue-800 disabled:opacity-30 transition" title="Move Down" aria-label="Move Question Down">
              &#9660;
            </button>
          </div>
          <button
            class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition"
            @click="removeQuestion(i)" title="Remove Question" aria-label="Remove Question">
            &#10006;
          </button>

          <input x-model="q.label" class="w-full border p-2 mb-2 rounded focus:outline-none focus:ring" placeholder="Enter your question" aria-label="Question Text" />
          <select x-model="q.type" class="w-full border p-2 mb-2 rounded"
            @change="onTypeChange(q)" aria-label="Question Type">
            <template x-for="type in fieldTypes" :key="type.value">
              <option :value="type.value" x-text="type.emoji + ' ' + type.label"></option>
            </template>
          </select>
          <label class="inline-flex items-center space-x-2 mb-2">
            <input type="checkbox" x-model="q.required" />
            <span>Required</span>
          </label>

          <!-- (Dynamic field-specific controls omitted for brevity; include as in earlier templates) -->

        </div>
      </template>

      <div class="flex justify-end mt-6">
        <button @click="validateAndSubmit"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 focus:ring-2 focus:ring-green-400"
        >Save Form</button>
      </div>

      <div class="mt-10" aria-live="polite" aria-atomic="true">
        <h2 class="text-xl font-semibold mb-4">Preview</h2>
        <h3 class="text-lg font-bold" x-text="form.title"></h3>
        <p class="text-gray-600 mb-4" x-text="form.description"></p>
        <!-- You can loop over form.questions here for a live preview -->
      </div>
    </div>
    <!-- /FORM BODY -->

    <!-- Editable Footer -->
    <footer class="bg-white rounded-b border-t px-6 py-5">
      <div class="flex flex-col sm:flex-row items-center justify-between text-gray-600">
        <div>
          <input x-model="footer.companyName"
            class="font-semibold bg-transparent border-b border-dashed focus:border-blue-400 focus:outline-none px-1 text-base"
            />
          <span class="text-xs">| © <span x-text="footer.year"></span> All rights reserved.</span>
          <br>
          <input x-model="footer.address"
            class="text-xs bg-transparent border-b border-dashed focus:border-blue-400 focus:outline-none px-1 mt-1"
            style="min-width:180px"
            />
        </div>
        <div class="mt-4 sm:mt-0 flex flex-col items-center sm:items-end">
          <span class="text-xs">
            Support:
            <input x-model="footer.email" type="email"
              class="text-blue-500 bg-transparent border-b border-dashed focus:border-blue-400 focus:outline-none px-1"
              style="min-width:150px"/>
          </span>
          <span class="text-xs mt-1">
            Call us:
            <input x-model="footer.phone"
              class="text-blue-500 bg-transparent border-b border-dashed focus:border-blue-400 focus:outline-none px-1"
              style="min-width:110px"/>
          </span>
        </div>
      </div>
    </footer>
    <!-- /FOOTER -->
  </div>
</main>

<script>
function formBuilder() {
  return {
    header: {
      logoUrl: "https://placehold.co/80x80?text=Logo",
      companyName: "Your Company",
      profileName: "User"
    },
    footer: {
      companyName: "Your Company Pvt. Ltd.",
      year: new Date().getFullYear(),
      address: "123, Main Street, City, Country",
      email: "support@yourcompany.com",
      phone: "+1 123-456-7890"
    },
    form: { title: '', description: '', questions: [] },
    addFieldDropdown: false,
    fieldTypes: [
      { value: 'name', label: 'Name', emoji: '🧑' },
      { value: 'mobile', label: 'Mobile Number', emoji: '📱' },
      { value: 'email', label: 'Email', emoji: '✉️' },
      { value: 'short', label: 'Short Answer', emoji: '📝' },
      { value: 'paragraph', label: 'Paragraph', emoji: '📄' },
      { value: 'multiple', label: 'Multiple Choice', emoji: '✅' },
      { value: 'single', label: 'Single Choice', emoji: '🔘' },
      { value: 'location', label: 'Location', emoji: '📍' },
      { value: 'file', label: 'File Upload', emoji: '📁' },
      { value: 'age', label: 'Age', emoji: '🎂' },
      { value: 'choice', label: 'Choice', emoji: '🔘' },
      { value: 'text', label: 'Text', emoji: '🔤' },
      { value: 'rating', label: 'Rating', emoji: '👍' },
      { value: 'date', label: 'Date', emoji: '📅' },
      { value: 'ranking', label: 'Ranking', emoji: '↕️' },
      { value: 'likert', label: 'Likert', emoji: '📊' },
      { value: 'nps', label: 'Net Promoter Score®', emoji: '🏁' },
      { value: 'section', label: 'Section', emoji: '📂' }
    ],
    init() { this.footer.year = new Date().getFullYear(); },
    addField(type) {
      const q = { label: '', type, required: false, answer: '' };
      if (['multiple', 'single', 'choice'].includes(type)) q.options = ['Option 1', 'Option 2'];
      if (type === 'location') q.answer = { address: '', lat: '', lng: '' };
      this.form.questions.push(q);
      this.addFieldDropdown = false;
    },
    removeQuestion(i) { this.form.questions.splice(i, 1); },
    moveQuestionUp(i) {
      if (i > 0) [this.form.questions[i - 1], this.form.questions[i]] = [this.form.questions[i], this.form.questions[i - 1]];
    },
    moveQuestionDown(i) {
      if (i < this.form.questions.length - 1) [this.form.questions[i + 1], this.form.questions[i]] = [this.form.questions[i], this.form.questions[i + 1]];
    },
    onLogoChange(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => { this.header.logoUrl = e.target.result; };
        reader.readAsDataURL(file);
      }
    },
    onTypeChange(q) {
      if (['multiple', 'single', 'choice'].includes(q.type)) q.options = ['Option 1', 'Option 2'];
      else if (q.options) delete q.options;
      if (q.type === 'location') q.answer = { address: '', lat: '', lng: '' };
      else q.answer = '';
    },
    validateAndSubmit() {
      if (!this.form.title || this.form.questions.length === 0) {
        alert("Title and at least one question are required.");
        return;
      }
      // Send form to backend
      fetch('/forms', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        },
        body: JSON.stringify({
          ...this.form,
          header: this.header,
          footer: this.footer,
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.form_id) {
          window.location.href = `/dashboard`;
        } else {
          alert('Failed to save. Try again.');
        }
      })
      .catch(() => alert('Failed to save. Try again.'));
    }
  }
}
</script>
</body>
</html>
