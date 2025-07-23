@php
    use App\Helpers\LocaleHelper;
    $supportedLocales = LocaleHelper::getSupportedLocales();
    $currentLocale = app()->getLocale();
@endphp

<div class="multilingual-field mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }} <span class="text-red-500">*</span>
    </label>

    <!-- Locale tabs -->
    <div class="border-b border-gray-200 mb-3">
        <nav class="-mb-px flex space-x-4" aria-label="Tabs">
            @foreach ($supportedLocales as $locale)
                <button type="button"
                    class="locale-tab py-2 px-3 inline-flex items-center border-b-2 font-medium text-sm {{ $locale['code'] === $currentLocale ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    onclick="showLocaleContent('{{ $name }}', '{{ $locale['code'] }}', this)">
                    <span class="mr-1">{{ $locale['flag'] }}</span>
                    {{ $locale['native'] }}
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Locale content -->
    @foreach ($supportedLocales as $locale)
        <div id="{{ $name }}_{{ $locale['code'] }}_content"
            class="locale-content {{ $locale['code'] !== $currentLocale ? 'hidden' : '' }}">
            @if ($type === 'textarea')
                <textarea name="{{ $name }}_translations[{{ $locale['code'] }}]"
                    id="{{ $name }}_{{ $locale['code'] }}" rows="{{ $rows ?? 4 }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="{{ $placeholder ?? '' }} ({{ $locale['native'] }})">{{ old($name . '_translations.' . $locale['code'], $value[$locale['code']] ?? '') }}</textarea>
            @else
                <input type="text" name="{{ $name }}_translations[{{ $locale['code'] }}]"
                    id="{{ $name }}_{{ $locale['code'] }}"
                    value="{{ old($name . '_translations.' . $locale['code'], $value[$locale['code']] ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="{{ $placeholder ?? '' }} ({{ $locale['native'] }})">
            @endif
        </div>
    @endforeach

    @error($name . '_translations.*')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    function showLocaleContent(fieldName, locale, clickedTab) {
        // Hide all locale contents for this field
        document.querySelectorAll(`[id^="${fieldName}_"][id$="_content"]`).forEach(content => {
            content.classList.add('hidden');
        });

        // Show the selected locale content
        document.getElementById(`${fieldName}_${locale}_content`).classList.remove('hidden');

        // Update tab styles
        clickedTab.parentElement.querySelectorAll('.locale-tab').forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });

        clickedTab.classList.remove('border-transparent', 'text-gray-500');
        clickedTab.classList.add('border-blue-500', 'text-blue-600');
    }
</script>
