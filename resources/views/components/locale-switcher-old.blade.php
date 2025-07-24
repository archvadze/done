@php
    use App\Helpers\LocaleHelper;
    $supportedLocales = LocaleHelper::getSupportedLocales();
    $currentLocale = LocaleHelper::getCurrentLocale();
@endphp

<div class="locale-switcher dropdown">
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        <span class="me-1">{{ $currentLocale['flag'] }}</span>
        <span class="d-none d-md-inline">{{ $currentLocale['native'] }}</span>
        <span class="d-md-none">{{ $currentLocale['code'] }}</span>
    </button>
    <ul class="dropdown-menu">
        @foreach ($supportedLocales as $locale)
            <li>
                <a class="dropdown-item {{ app()->getLocale() === $locale['code'] ? 'active' : '' }}"
                    href="{{ route('locale.switch', $locale['code']) }}">
                    <span class="me-2">{{ $locale['flag'] }}</span>
                    <span>{{ $locale['native'] }}</span>
                    <small class="text-muted ms-1">({{ $locale['name'] }})</small>
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
    .locale-switcher .dropdown-item.active {
        background-color: var(--bs-primary);
        color: white;
    }

    .locale-switcher .dropdown-item:hover {
        background-color: var(--bs-light);
    }

    .locale-switcher .dropdown-item.active:hover {
        background-color: var(--bs-primary);
        opacity: 0.9;
    }
</style>
