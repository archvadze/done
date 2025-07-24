@extends('layouts.admin')

@section('title', 'Languages Management')
@section('subtitle', 'Manage multilingual support and content localization')

@section('content')
    <!-- Language Settings Overview -->
    <div class="admin-stats-card mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Multilingual System Overview</h3>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-blue-600 text-xl">ℹ️</span>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-900">How Multilingual Content Works</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>When you <strong>activate</strong> a language, it becomes available throughout the platform
                            </li>
                            <li>Users will see language switcher options for all active languages</li>
                            <li>When adding artworks, users must provide content in <strong>all active languages</strong>
                            </li>
                            <li>Content is stored in JSON format: <code>{"en": "Title", "ka": "სათაური", "de":
                                    "Titel"}</code></li>
                            <li>The system automatically displays content in the user's selected language</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-green-600 text-2xl mr-3">✅</span>
                    <div>
                        <div class="text-sm font-medium text-green-900">Active Languages</div>
                        <div class="text-2xl font-bold text-green-600">{{ $languages->where('is_active', true)->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-gray-600 text-2xl mr-3">⭐</span>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Default Language</div>
                        <div class="text-lg font-bold text-gray-600">
                            {{ $languages->where('is_default', true)->first()->native_name ?? 'None' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-blue-600 text-2xl mr-3">🌍</span>
                    <div>
                        <div class="text-sm font-medium text-blue-900">Total Supported</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $languages->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Languages Management -->
    <div class="admin-stats-card">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Available Languages</h3>
            <button type="button"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                onclick="addLanguage()">
                Add Language
            </button>
        </div>

        @if ($languages->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Language</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($languages as $language)
                            <tr>
                                <!-- Language Info -->
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">{{ $language->flag_emoji }}</span>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $language->native_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $language->name }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Language Code -->
                                <td>
                                    <span class="admin-badge admin-badge-info font-mono">{{ $language->code }}</span>
                                </td>

                                <!-- Status -->
                                <td>
                                    @if ($language->is_active)
                                        <span class="admin-badge admin-badge-success">Active</span>
                                    @else
                                        <span class="admin-badge admin-badge-danger">Inactive</span>
                                    @endif
                                </td>

                                <!-- Default -->
                                <td>
                                    @if ($language->is_default)
                                        <span class="admin-badge admin-badge-warning">Default</span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                <!-- Sort Order -->
                                <td>
                                    <span class="text-sm text-gray-900">{{ $language->sort_order }}</span>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <!-- Toggle Active Status -->
                                        @if ($language->is_active)
                                            @if (!$language->is_default)
                                                <button class="text-red-600 hover:text-red-700 text-sm"
                                                    onclick="toggleLanguage({{ $language->id }}, false, '{{ $language->name }}')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-sm">Cannot deactivate</span>
                                            @endif
                                        @else
                                            <button class="text-green-600 hover:text-green-700 text-sm"
                                                onclick="toggleLanguage({{ $language->id }}, true, '{{ $language->name }}')">
                                                Activate
                                            </button>
                                        @endif

                                        <!-- Set as Default -->
                                        @if ($language->is_active && !$language->is_default)
                                            <button class="text-blue-600 hover:text-blue-700 text-sm"
                                                onclick="setDefault({{ $language->id }}, '{{ $language->name }}')">
                                                Set Default
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">🌍</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No languages configured</h3>
                <p class="text-gray-500">Add your first language to enable multilingual support.</p>
            </div>
        @endif
    </div>

    <!-- Content Migration Notice -->
    @if ($languages->where('is_active', true)->count() > 1)
        <div class="admin-stats-card mt-6">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="text-amber-600 text-xl">⚠️</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-amber-900">Content Migration Required</h4>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>When you activate/deactivate languages, existing content may need to be updated:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>Existing artworks with single-language content will need translation</li>
                                <li>Users will be prompted to provide missing translations when editing</li>
                                <li>New content must include all active languages</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function toggleLanguage(languageId, isActive, languageName) {
            const action = isActive ? 'activate' : 'deactivate';
            if (confirm(
                    `Are you sure you want to ${action} "${languageName}"? This will affect content display across the platform.`
                    )) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/languages/${languageId}/status`;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add method spoofing
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);

                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'is_active';
                statusInput.value = isActive ? '1' : '0';
                form.appendChild(statusInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function setDefault(languageId, languageName) {
            if (confirm(
                    `Set "${languageName}" as the default language? This will be the fallback language for all content.`)) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/languages/${languageId}/default`;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add method spoofing
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function addLanguage() {
            alert(
                'Add language functionality will be implemented soon. For now, languages can be added via database seeding.');
        }
    </script>
@endpush
