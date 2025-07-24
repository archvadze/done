@php
    $currentLocale = app()->getLocale();
    $supportedLocales = [
        'en' => ['name' => 'English', 'flag' => '🇺🇸'],
        'ka' => ['name' => 'ქართული', 'flag' => '🇬🇪'],
        'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
    ];
@endphp

<div class="relative">
    <button onclick="toggleLanguageDropdown()"
        class="flex items-center space-x-1 text-gray-600 hover:text-gray-900 px-2 py-1 rounded">
        <span>{{ $supportedLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="hidden md:inline">{{ $supportedLocales[$currentLocale]['name'] ?? 'Language' }}</span>
        <span class="md:hidden">{{ strtoupper($currentLocale) }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div id="languageDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50">
        @foreach ($supportedLocales as $code => $locale)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 {{ $currentLocale === $code ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                <span class="mr-3">{{ $locale['flag'] }}</span>
                <span>{{ $locale['name'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<script>
    function toggleLanguageDropdown() {
        const dropdown = document.getElementById('languageDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('languageDropdown');
        const button = event.target.closest('button');

        if (!button || button.getAttribute('onclick') !== 'toggleLanguageDropdown()') {
            dropdown.classList.add('hidden');
        }
    });
</script>
