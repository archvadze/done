@php
    $currentLocale = app()->getLocale();
    $activeLanguages = \App\Models\Language::active();
    $supportedLocales = [];
    
    foreach ($activeLanguages as $language) {
        $supportedLocales[$language->code] = [
            'name' => $language->name,
            'flag' => $language->flag_emoji,
            'native' => $language->native_name
        ];
    }
    
    // Fallback if no active languages
    if (empty($supportedLocales)) {
        $supportedLocales = [
            'en' => ['name' => 'English', 'flag' => '��', 'native' => 'English']
        ];
    }
@endphp

<div class="relative inline-block text-left">
    <button onclick="toggleLanguageDropdown()" 
            class="inline-flex items-center justify-center w-full px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            type="button">
        <span class="mr-2 text-lg">{{ $supportedLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="hidden sm:inline">{{ $supportedLocales[$currentLocale]['native'] ?? 'Language' }}</span>
        <span class="sm:hidden">{{ strtoupper($currentLocale) }}</span>
        <svg class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div id="languageDropdown" 
         class="hidden absolute right-0 z-50 w-56 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            @foreach ($supportedLocales as $code => $locale)
                <a href="{{ route('locale.switch', $code) }}" 
                   class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $currentLocale === $code ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="mr-3 text-lg">{{ $locale['flag'] }}</span>
                    <div class="flex flex-col">
                        <span class="font-medium">{{ $locale['native'] }}</span>
                        <span class="text-xs text-gray-500">{{ $locale['name'] }}</span>
                    </div>
                    @if ($currentLocale === $code)
                        <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
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
    
    if (!button || !button.hasAttribute('onclick') || button.getAttribute('onclick') !== 'toggleLanguageDropdown()') {
        dropdown.classList.add('hidden');
    }
});

// Close dropdown on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('languageDropdown').classList.add('hidden');
    }
});
</script>
