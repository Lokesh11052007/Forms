<!DOCTYPE html>
<html lang="en" x-data="formBuilder()" x-init="init()" x-cloak>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Builder</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

          <!-- Field-specific controls/validation -->
          <template x-if="q.type === 'location'">
            <div class="mt-3 space-y-2">
              <input type="text" x-model="q.answer.address" class="w-full border p-2 rounded"
                     :class="{'border-red-400': q.error && q.required && !q.answer.address }"
                     :placeholder="'Address' + (q.required ? ' *':'')" />
              <div class="flex space-x-2">
                <input type="text" x-model="q.answer.lat" class="w-full border p-2 rounded" placeholder="Latitude" />
                <input type="text" x-model="q.answer.lng" class="w-full border p-2 rounded" placeholder="Longitude" />
              </div>
              <button type="button"
                class="text-sm text-blue-600 hover:underline mt-1"
                @click="fetchLocation(q)">📍 Detect Location</button>
              <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer.address">Address Required</span>
            </div>
          </template>

          <!-- Multiple/Single/Choice Options -->
          <template x-if="['multiple', 'single', 'choice'].includes(q.type)">
            <div class="mt-3">
              <template x-for="(opt, idx) in q.options" :key="idx">
                <div class="flex items-center space-x-2 mb-2">
                  <input type="text" x-model="q.options[idx]" class="flex-1 border p-2 rounded" :placeholder="`Option ${idx+1}`" />
                  <div class="flex flex-col">
                    <button @click="moveOptionUp(q, idx)" :disabled="idx === 0"
                      class="text-gray-600 hover:text-black disabled:opacity-30 text-sm transition" aria-label="Move Option Up">&#9650;</button>
                    <button @click="moveOptionDown(q, idx)" :disabled="idx === q.options.length - 1"
                      class="text-gray-600 hover:text-black disabled:opacity-30 text-sm transition" aria-label="Move Option Down">&#9660;</button>
                  </div>
                  <button @click="removeOption(q, idx)" class="text-red-600 hover:text-red-800 transition" aria-label="Remove Option">&#10006;</button>
                </div>
              </template>
              <button @click="addOption(q)" class="text-sm text-blue-600 hover:underline">+ Add Option</button>
            </div>
          </template>

          <!-- Type: File -->
          <template x-if="q.type === 'file'">
            <div class="mt-3 flex flex-col gap-1 text-gray-500 text-sm italic">
              <input type="file" class="block" @change="onFileChange(q, $event)" />
              <span x-text="q.answer ? q.answer.name : 'No file chosen'"></span>
              <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">File Required</span>
            </div>
          </template>

          <!-- Type: Age (only numbers allowed) -->
          <template x-if="q.type === 'age'">
            <input type="number" x-model="q.answer" min="0" max="120"
                class="w-full border p-2 mt-3 rounded"
                :class="{'border-red-400': q.error && q.required && !q.answer}"
                :placeholder="'Age' + (q.required ? ' *':'')"/>
            <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">Age Required</span>
          </template>

          <!-- Type: Mobile (number only, basic pattern check) -->
          <template x-if="q.type === 'mobile'">
            <input type="tel" x-model="q.answer"
                   class="w-full border p-2 mt-3 rounded"
                   pattern="[0-9]{7,15}"
                   inputmode="numeric"
                   maxlength="15"
                   @input="q.answer = q.answer.replace(/[^0-9]/g, '')"
                   :class="{'border-red-400': q.error && q.required && !/^([0-9]{7,15})$/.test(q.answer)}"
                   :placeholder="'Mobile' + (q.required ? ' *':'')" />
            <span class="text-xs text-red-500" x-show="q.error && q.required && !/^([0-9]{7,15})$/.test(q.answer)">A valid mobile is required</span>
          </template>

          <!-- Type: Email (type=email) -->
          <template x-if="q.type === 'email'">
            <input type="email" x-model="q.answer"
                   class="w-full border p-2 mt-3 rounded"
                   :class="{'border-red-400': q.error && q.required && !/^[\w\-.]+@[\w\-]+\.[a-z]{2,}$/.test(q.answer)}"
                   :placeholder="'Email' + (q.required ? ' *':'')" />
            <span class="text-xs text-red-500" x-show="q.error && q.required && !/^[\w\-.]+@[\w\-]+\.[a-z]{2,}$/i.test(q.answer)">A valid email is required</span>
          </template>

          <!-- Type: Date -->
          <template x-if="q.type === 'date'">
            <input type="date" x-model="q.answer" class="w-full border p-2 mt-3 rounded"
              :class="{'border-red-400': q.error && q.required && !q.answer}" />
            <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">Date Required</span>
          </template>

          <!-- Type: Rating -->
          <template x-if="q.type === 'rating'">
            <div class="mt-3 flex space-x-2">
              <template x-for="n in 5">
                <label>
                  <input type="radio" :value="n" x-model="q.answer"
                    class="hidden peer" />
                  <span :class="{'text-yellow-500': q.answer >= n, 'text-gray-300': q.answer < n}" class="peer-checked:text-yellow-400 text-2xl cursor-pointer">★</span>
                </label>
              </template>
            </div>
            <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">Rating Required</span>
          </template>

          <!-- Placeholders for advanced types -->
          <template x-if="['likert','ranking','nps','section'].includes(q.type)">
            <div class="mt-3 text-gray-500 italic bg-gray-100 border p-2 rounded">
              [Preview not implemented for <span x-text="q.type"></span> field]
            </div>
          </template>

          <!-- Type: Paragraph -->
          <template x-if="q.type === 'paragraph'">
            <textarea x-model="q.answer" rows="3" class="w-full border p-2 mt-3 rounded"
              :class="{'border-red-400': q.error && q.required && !q.answer}"
              :placeholder="(q.required ? '* ' : '') + 'Your answer'"></textarea>
            <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">This is required</span>
          </template>

          <!-- Type: Short/Text -->
          <template x-if="['short','text','name'].includes(q.type)">
            <input x-model="q.answer" class="w-full border p-2 mt-3 rounded"
              :class="{'border-red-400': q.error && q.required && !q.answer}"
              :placeholder="(q.required ? '* ' : '') + 'Your answer'" />
            <span class="text-xs text-red-500" x-show="q.error && q.required && !q.answer">This is required</span>
          </template>
        </div>
      </template>

      <div class="flex justify-end mt-6">
        <button @click="validateAndSubmit"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 focus:ring-2 focus:ring-green-400"
        >Submit</button>
      </div>

      <div class="mt-10" aria-live="polite" aria-atomic="true">
        <h2 class="text-xl font-semibold mb-4">Preview</h2>
        <h3 class="text-lg font-bold" x-text="form.title"></h3>
        <p class="text-gray-600 mb-4" x-text="form.description"></p>
        <template x-for="(q, i) in form.questions" :key="i">
          <div class="mb-4">
            <label class="block font-medium mb-1" x-text="q.label"></label>
            <!-- Age -->
            <template x-if="q.type === 'age'">
              <select disabled class="w-full border p-2 rounded">
                <option disabled selected>Select Age</option>
                <template x-for="n in 100">
                  <option :selected="q.answer == n" x-text="n + ' years'"></option>
                </template>
              </select>
            </template>
            <!-- Location -->
            <template x-if="q.type === 'location'">
              <div class="space-y-2">
                <input type="text" class="w-full border p-2 rounded"
                  :value="q.answer.address" placeholder="Address" disabled />
                <div class="flex space-x-2">
                  <input type="text" class="w-full border p-2 rounded"
                    :value="q.answer.lat" placeholder="Latitude" disabled />
                  <input type="text" class="w-full border p-2 rounded"
                    :value="q.answer.lng" placeholder="Longitude" disabled />
                </div>
              </div>
            </template>
            <!-- Choice/radio -->
            <template x-if="['single','choice'].includes(q.type)">
              <div class="space-y-2">
                <template x-for="(opt, idx) in q.options" :key="idx">
                  <label class="inline-flex items-center space-x-2">
                    <input type="radio" :name="'q'+i" :checked="q.answer==opt" disabled />
                    <span x-text="opt"></span>
                  </label>
                </template>
              </div>
            </template>
            <!-- Multiple -->
            <template x-if="q.type === 'multiple'">
              <div class="space-y-2">
                <template x-for="(opt, idx) in q.options" :key="idx">
                  <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" :checked="Array.isArray(q.answer)&&q.answer.includes(opt)" disabled />
                    <span x-text="opt"></span>
                  </label>
                </template>
              </div>
            </template>
            <!-- File -->
            <template x-if="q.type === 'file'">
              <div><span x-text="q.answer && q.answer.name ? q.answer.name : 'No file chosen'"></span></div>
            </template>
            <!-- Rating -->
            <template x-if="q.type === 'rating'">
              <div class="flex space-x-1">
                <template x-for="n in 5">
                  <span :class="{'text-yellow-400': q.answer >= n, 'text-gray-200': q.answer < n}" class="text-2xl">★</span>
                </template>
              </div>
            </template>
            <!-- Date -->
            <template x-if="q.type === 'date'">
              <input type="date" class="w-full border p-2 rounded" :value="q.answer" disabled/>
            </template>
            <!-- Advanced Types -->
            <template x-if="['likert','ranking','nps','section'].includes(q.type)">
              <div class="text-gray-500 italic bg-gray-100 border p-2 rounded">
                [Preview for <span x-text="q.type"></span> field]
              </div>
            </template>
            <!-- Paragraph/Text/Short -->
            <template x-if="['short','text','name','paragraph'].includes(q.type)">
              <input type="text" class="w-full border p-2 rounded" :value="q.answer" disabled />
            </template>
            <!-- Mobile/Email (text preview) -->
            <template x-if="['mobile','email'].includes(q.type)">
              <input type="text" class="w-full border p-2 rounded" :value="q.answer" disabled />
            </template>
          </div>
        </template>
        <div class="text-sm text-gray-500 mt-6 text-right"><span x-text="currentTime"></span></div>
        <div x-show="responseUrl" class="mt-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Response URL</label>
          <input type="text" x-model="responseUrl" readonly
            class="w-full border border-blue-300 bg-blue-50 text-blue-800 px-4 py-2 rounded focus:outline-none cursor-pointer"
            @click="$event.target.select()" />
          <a :href="responseUrl" target="_blank"
            class="text-blue-600 underline mt-2 inline-block">Click here to open the form</a>
        </div>
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
              style="min-width:150px"
              />
          </span>
          <span class="text-xs mt-1">
            Call us:
            <input x-model="footer.phone"
              class="text-blue-500 bg-transparent border-b border-dashed focus:border-blue-400 focus:outline-none px-1"
              style="min-width:110px"
              />
          </span>
        </div>
      </div>
    </footer>
    <!-- /FOOTER -->
  </div>
</main>

<script>
function formBuilder() {
  let timer;
  return {
    header: {
      logoUrl: "https://placehold.co/80x80?text=Logo", // default logo url
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
    responseUrl: '',
    currentTime: '',
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
    init() {
      this.updateClock();
      timer = setInterval(() => this.updateClock(), 1000);
      document.addEventListener('alpine:destroy', () => clearInterval(timer));
    },
    updateClock() {
      this.currentTime = new Date().toLocaleString();
      this.footer.year = new Date().getFullYear();
    },
    addField(type) {
      const fieldSetup = {
        location: { answer: { address: '', lat: '', lng: '' } },
        multiple: { options: ['Option 1', 'Option 2'] },
        single:   { options: ['Option 1', 'Option 2'] },
        choice:   { options: ['Option 1', 'Option 2'] },
        file:     { answer: null },
        age:      { answer: '' },
        mobile:   { answer: '' },
        email:    { answer: '' },
        rating:   { answer: '' },
        date:     { answer: '' },
        paragraph:{ answer: '' },
        name:     { answer: '' },
        short:    { answer: '' },
        text:     { answer: '' },
        default:  {}
      };
      const setup = fieldSetup[type] || fieldSetup['default'];
      const q = { label: '', type, required: false, ...setup, error: false };
      if (!q.answer) q.answer = '';
      if (!q.options && ['single','multiple','choice'].includes(type)) q.options = ['Option 1', 'Option 2'];
      this.form.questions.push(q);
      this.addFieldDropdown = false;
    },
    removeQuestion(i) { this.form.questions.splice(i, 1); },
    onTypeChange(q) {
      const resets = {
        location: () => { q.answer = { address: '', lat: '', lng: '' }; delete q.options; },
        multiple: () => { q.options = ['Option 1', 'Option 2']; q.answer = []; },
        single:   () => { q.options = ['Option 1', 'Option 2']; q.answer = ''; },
        choice:   () => { q.options = ['Option 1', 'Option 2']; q.answer = ''; },
        file:     () => { q.answer = null; delete q.options; },
        age:      () => { q.answer = ''; delete q.options; },
        mobile:   () => { q.answer = ''; delete q.options; },
        email:    () => { q.answer = ''; delete q.options; },
        rating:   () => { q.answer = ''; delete q.options; },
        date:     () => { q.answer = ''; delete q.options; },
        paragraph:() => { q.answer = ''; delete q.options; },
        name:     () => { q.answer = ''; delete q.options; },
        short:    () => { q.answer = ''; delete q.options; },
        text:     () => { q.answer = ''; delete q.options; }
      };
      if (resets[q.type]) {
        resets[q.type]();
      } else {
        q.answer = '';
        delete q.options;
      }
      q.error = false;
    },
    addOption(q) { q.options ? q.options.push('') : q.options = ['']; },
    removeOption(q, idx) { if (q.options) q.options.splice(idx, 1); },
    moveOptionUp(q, idx) {
      if (idx > 0) [q.options[idx-1], q.options[idx]] = [q.options[idx], q.options[idx-1]]
    },
    moveOptionDown(q, idx) {
      if (q.options && idx < q.options.length - 1) [q.options[idx+1], q.options[idx]] = [q.options[idx], q.options[idx+1]];
    },
    moveQuestionUp(i) {
      if (i > 0) [this.form.questions[i - 1], this.form.questions[i]] = [this.form.questions[i], this.form.questions[i - 1]]
    },
    moveQuestionDown(i) {
      if (i < this.form.questions.length - 1) [this.form.questions[i + 1], this.form.questions[i]] = [this.form.questions[i], this.form.questions[i + 1]]
    },
    fetchLocation(q) {
      if (!navigator.geolocation) return alert('Geolocation not supported.');
      navigator.geolocation.getCurrentPosition(
        pos => { q.answer.lat = pos.coords.latitude.toFixed(6); q.answer.lng = pos.coords.longitude.toFixed(6); },
        err => alert('Location error: ' + err.message)
      );
    },
    validateAndSubmit() {
      // Validate each required question
      let valid = true;
      this.form.questions.forEach(q => {
        if (!q.required) { q.error = false; return; }
        switch(q.type) {
          case 'location':
            q.error = !(q.answer.address);
            valid = valid && !q.error;
            break;
          case 'multiple':
            q.error = !Array.isArray(q.answer) ? true : q.answer.length == 0;
            valid = valid && !q.error;
            break;
          case 'single':
          case 'choice':
            q.error = !q.answer;
            valid = valid && !q.error;
            break;
          case 'file':
            q.error = !q.answer;
            valid = valid && !q.error;
            break;
          case 'age':
            q.error = !(q.answer && !isNaN(Number(q.answer)));
            valid = valid && !q.error;
            break;
          case 'mobile':
            q.error = !(q.answer && /^([0-9]{7,15})$/.test(q.answer));
            valid = valid && !q.error;
            break;
          case 'email':
            q.error = !(q.answer && /^[\w\-.]+@[\w\-]+\.[a-z]{2,}$/i.test(q.answer));
            valid = valid && !q.error;
            break;
          case 'date':
            q.error = !q.answer;
            valid = valid && !q.error;
            break;
          case 'rating':
            q.error = !q.answer;
            valid = valid && !q.error;
            break;
          case 'paragraph':
          case 'short':
          case 'text':
          case 'name':
            q.error = !q.answer;
            valid = valid && !q.error;
            break;
          default:
            q.error = false;
        }
      });
      if (!valid) {
        alert('Please correct fields highlighted in red and marked as required.');
        return;
      }
      this.submitForm();
    },
    submitForm() {
      const url = `${window.location.origin}/form/respond?data=${encodeURIComponent(JSON.stringify(this.form))}`;
      this.responseUrl = url;
      alert('Form submitted! URL below.');
    },
    // Logo uploader, updates reactive header.logoUrl
    onLogoChange(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => { this.header.logoUrl = e.target.result; };
        reader.readAsDataURL(file);
      }
    },
    // Save selected file in q.answer
    onFileChange(q, event) {
      const file = event.target.files[0];
      q.answer = file || null;
    }
  }
}
</script>
</body>
</html>
