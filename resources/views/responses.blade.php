<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Respond to Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>[x-cloak] { display:none !important; }</style>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-3xl mx-auto bg-white p-0 rounded shadow flex flex-col">

    <!-- HEADER: rendered from values supplied by your builder -->
    <header class="bg-white rounded-t px-6 py-4 flex items-center justify-between border-b">
      <div class="flex items-center space-x-3">
        <img src="{{ $form['header']['logoUrl'] ?? 'https://placehold.co/80x80?text=Logo' }}"
             alt="Company Logo"
             class="h-10 w-10 rounded-full object-cover border" />
        <span class="text-lg font-bold text-gray-700">
          {{ $form['header']['companyName'] ?? 'Your Company' }}
        </span>
      </div>
      <div class="flex items-center space-x-2">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($form['header']['profileName'] ?? 'User') }}"
             alt="Profile"
             class="h-8 w-8 rounded-full border" />
        <span class="hidden sm:inline text-gray-700 font-medium">
          {{ $form['header']['profileName'] ?? 'User' }}
        </span>
      </div>
    </header>

    <form method="POST" action="{{ route('form.response.submit') }}" enctype="multipart/form-data" autocomplete="off">
      @csrf
      @if(isset($form['id']))
        <input type="hidden" name="form_id" value="{{ $form['id'] }}">
      @endif
      <input type="hidden" name="form_data" value="{{ $formJson }}">

      <div class="px-6 pt-6 pb-0">
        <h1 class="text-2xl font-bold mb-2">{{ $form['title'] ?? 'Untitled Form' }}</h1>
        <p class="mb-4 text-gray-600">{{ $form['description'] ?? '' }}</p>

        @if(session('success'))
          <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @foreach (($form['questions'] ?? []) as $index => $q)
          <div class="mb-6">
            <label class="block font-semibold mb-2">
              {{ $q['label'] ?? 'Untitled Question' }}
              @if(!empty($q['required']))
                <span class="text-red-500">*</span>
              @endif
            </label>
            @switch($q['type'])
              @case('text')
              @case('short')
              @case('name')
                <input type="text" name="answers[{{ $index }}]"
                       class="w-full border px-3 py-2 rounded"
                       @if(!empty($q['required'])) required @endif
                       value="{{ old('answers.'.$index) }}">
                @break

              @case('paragraph')
                <textarea name="answers[{{ $index }}]" rows="4"
                          class="w-full border px-3 py-2 rounded"
                          @if(!empty($q['required'])) required @endif>{{ old('answers.'.$index) }}</textarea>
                @break

              @case('email')
                <input type="email" name="answers[{{ $index }}]"
                       class="w-full border px-3 py-2 rounded"
                       @if(!empty($q['required'])) required @endif
                       value="{{ old('answers.'.$index) }}"
                       pattern="^[\w\-.]+@[\w\-]+\.[a-z]{2,}$">
                @break

              @case('mobile')
                <input type="tel" name="answers[{{ $index }}]"
                       pattern="[0-9]{10,15}"
                       maxlength="15"
                       inputmode="numeric"
                       class="w-full border px-3 py-2 rounded"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       autocomplete="off"
                       @if(!empty($q['required'])) required @endif
                       placeholder="Mobile number"
                       value="{{ old('answers.'.$index) }}">
                @break

              @case('age')
                <input type="number" name="answers[{{ $index }}]"
                       min="1" max="120"
                       class="w-full border px-3 py-2 rounded"
                       @if(!empty($q['required'])) required @endif
                       value="{{ old('answers.'.$index) }}">
                @break

              @case('file')
                <input type="file" name="answers[{{ $index }}]"
                       class="w-full border px-3 py-2 rounded"
                       @if(!empty($q['required'])) required @endif>
                @break

              @case('location')
                <input type="text" name="answers[{{ $index }}]" id="location_{{ $index }}"
                       class="w-full border px-3 py-2 rounded"
                       readonly
                       @if(!empty($q['required'])) required @endif
                       value="{{ old('answers.'.$index) }}">
                <button type="button"
                        onclick="getLocation({{ $index }})"
                        class="mt-2 text-blue-500 underline">Use My Location</button>
                @break

              @case('single')
              @case('choice')
                @foreach($q['options'] ?? [] as $opt)
                  <label class="block">
                    <input type="radio"
                           name="answers[{{ $index }}]"
                           value="{{ $opt }}"
                           @if(!empty($q['required'])) required @endif
                           {{ old('answers.'.$index) == $opt ? 'checked' : '' }}>
                    {{ $opt }}
                  </label>
                @endforeach
                @break

              @case('multiple')
                @foreach($q['options'] ?? [] as $opt)
                  <label class="block">
                    <input type="checkbox"
                           name="answers[{{ $index }}][]"
                           value="{{ $opt }}"
                           {{ is_array(old('answers.'.$index)) && in_array($opt, old('answers.'.$index, [])) ? 'checked' : '' }}>
                    {{ $opt }}
                  </label>
                @endforeach
                @break

              @case('rating')
                @php
                  $style = $q['ratingStyle'] ?? 'stars';
                  $ratingEmojis = [
                    'stars' => ['&#9733;', '', 'text-yellow-400'],
                    'thumbs' => ['&#128077;', '', 'text-green-500'],
                    'smileys' => ['😞','😐','🙂','😊','😁'],
                    'flags' => ['&#x1F6A9;', '', 'text-red-500'],
                    'leaves' => ['&#127810;', '', 'text-green-600']
                  ];
                @endphp
                <div class="flex items-center space-x-2">
                  @for($n=1; $n<=5; $n++)
                    <label class="flex items-center space-x-1">
                      <input type="radio"
                        name="answers[{{ $index }}]"
                        value="{{ $n }}"
                        @if(!empty($q['required'])) required @endif
                        {{ old('answers.'.$index) == $n ? 'checked' : '' }}>
                      @if($style === 'smileys')
                        <span class="text-2xl">{!! $ratingEmojis['smileys'][$n-1] !!}</span>
                      @else
                        <span class="text-2xl {{ $ratingEmojis[$style][2] ?? '' }}">{!! $ratingEmojis[$style][0] !!}</span>
                      @endif
                    </label>
                  @endfor
                </div>
                @break

              @case('nps')
                <div>
                  <div class="flex space-x-2">
                    @for($n=0; $n<=10; $n++)
                      <label class="flex flex-col items-center text-xs">
                        <input type="radio"
                          name="answers[{{ $index }}]"
                          value="{{ $n }}"
                          @if(!empty($q['required'])) required @endif
                          {{ old('answers.'.$index) == $n ? 'checked' : '' }}>
                        <span class="w-6 h-6 flex items-center justify-center rounded-full
                          {{ old('answers.'.$index) == $n ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                          {{ $n }}
                        </span>
                      </label>
                    @endfor
                  </div>
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Not at all likely</span>
                    <span>Extremely likely</span>
                  </div>
                </div>
                @break

              @case('likert')
                @php
                  $likertStatements = $q['likertStatements'] ?? ['I am satisfied', 'I would recommend', 'It meets my needs'];
                  $likertName = "answers[$index]";
                  $oldLikert = old('answers.'.$index, []);
                @endphp
                <div class="space-y-2">
                  @foreach($likertStatements as $row)
                    <div class="flex items-center space-x-4">
                      <span class="flex-1 text-sm">{{ $row }}</span>
                      @for($n=1; $n<=5; $n++)
                        <label>
                          <input type="radio"
                                 name="{{ $likertName }}[{{ $row }}]"
                                 value="{{ $n }}"
                                 @if(!empty($q['required'])) required @endif
                                 {{ (isset($oldLikert[$row]) && $oldLikert[$row] == $n) ? 'checked' : '' }}>
                          <span class="text-gray-500 text-xs">{{ $n }}</span>
                        </label>
                      @endfor
                    </div>
                  @endforeach
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Strongly disagree</span>
                    <span>Strongly agree</span>
                  </div>
                </div>
                @break

              @case('ranking')
                @php
                  $options = $q['options'] ?? ['Option 1','Option 2','Option 3'];
                  $oldRanking = old('answers.'.$index, []);
                @endphp
                <div class="space-y-2">
                  @foreach($options as $pos => $opt)
                    <label class="block text-sm">
                      Position {{ $pos+1 }}:
                      <select name="answers[{{ $index }}][{{ $pos }}]" class="border ml-2 p-1 rounded" required>
                        <option value="">Select option</option>
                        @foreach($options as $choice)
                          <option value="{{ $choice }}" {{ isset($oldRanking[$pos]) && $oldRanking[$pos]==$choice ? 'selected' : '' }}>
                            {{ $choice }}
                          </option>
                        @endforeach
                      </select>
                    </label>
                  @endforeach
                  <div class="text-xs mt-1 text-gray-500">Set each option to its ranking (no duplicates).</div>
                </div>
                @break

              @case('date')
                <input type="date"
                  name="answers[{{ $index }}]"
                  class="w-full border px-3 py-2 rounded"
                  @if(!empty($q['required'])) required @endif
                  value="{{ old('answers.'.$index) }}">
                @break

              @case('section')
                <div class="italic text-gray-400">[Section – no input]</div>
                @break

              @default
                <input type="text"
                       name="answers[{{ $index }}]"
                       class="w-full border px-3 py-2 rounded"
                       @if(!empty($q['required'])) required @endif
                       value="{{ old('answers.'.$index) }}">
            @endswitch
          </div>
        @endforeach

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">
          Submit
        </button>
      </div>

      <!-- FOOTER: rendered from values supplied by your builder -->
      <footer class="bg-white rounded-b border-t px-6 py-5 mt-10">
        <div class="flex flex-col sm:flex-row items-center justify-between text-gray-600">
          <div>
            <span class="font-semibold">
              {{ $form['footer']['companyName'] ?? 'Your Company Pvt. Ltd.' }}
            </span>
            <span class="text-xs">
              | © {{ $form['footer']['year'] ?? date('Y') }} All rights reserved.
            </span>
            <br>
            <span class="text-xs">
              {{ $form['footer']['address'] ?? '123, Main Street, City, Country' }}
            </span>
          </div>
          <div class="mt-4 sm:mt-0 flex flex-col items-center sm:items-end">
            <span class="text-xs">
              Support:
              <a href="mailto:{{ $form['footer']['email'] ?? 'support@yourcompany.com' }}" class="text-blue-500 hover:underline">
                {{ $form['footer']['email'] ?? 'support@yourcompany.com' }}
              </a>
            </span>
            <span class="text-xs mt-1">
              Call us:
              <a href="tel:{{ $form['footer']['phone'] ?? '+11234567890' }}" class="text-blue-500 hover:underline">
                {{ $form['footer']['phone'] ?? '+1 123-456-7890' }}
              </a>
            </span>
          </div>
        </div>
      </footer>
    </form>
  </div>
  <script>
    function getLocation(index) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
            .then(response => response.json())
            .then(data => {
              document.getElementById(`location_${index}`).value = data.display_name ?? `${position.coords.latitude},${position.coords.longitude}`;
            })
            .catch(() => {
              alert("Unable to retrieve address from coordinates.");
            });
        }, function () {
          alert("Geolocation failed or was denied.");
        });
      } else {
        alert("Geolocation is not supported in your browser.");
      }
    }
  </script>
</body>
</html>
