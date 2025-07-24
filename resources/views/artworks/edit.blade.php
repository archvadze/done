<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit {{ $artwork->getTitle() }} - Acumen Craft</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .drop-zone {
            transition: all 0.3s ease;
        }

        .drop-zone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            transform: scale(1.02);
        }

        .file-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }

        .current-file {
            border: 2px solid #10b981;
            background-color: #ecfdf5;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                            🎨 Acumen Craft
                        </a>
                        <nav class="hidden md:flex space-x-4">
                            <a href="{{ route('artworks.show', $artwork) }}" class="text-blue-600 hover:text-blue-700">←
                                Back to Artwork</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('artworks.index') }}" class="text-gray-600 hover:text-gray-900">Gallery</a>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Artwork</h1>
                <p class="text-gray-600">Update your artwork details and settings</p>
            </div>

            <form id="artwork-edit-form" method="POST" action="{{ route('artworks.update', $artwork) }}"
                enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Current File Display -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📁 Current File</h2>

                        <!-- Current File Preview -->
                        <div class="current-file rounded-lg p-4 mb-4">
                            @if ($artwork->file_path)
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ basename($artwork->file_path) }}</h3>
                                        <p class="text-sm text-gray-600">
                                            {{ strtoupper(pathinfo($artwork->file_path, PATHINFO_EXTENSION)) }} •
                                            {{ number_format($artwork->file_size / 1024 / 1024, 2) }} MB •
                                            Uploaded {{ $artwork->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <span class="text-green-600 text-sm font-medium">✓ Current</span>
                                </div>

                                @if (Str::startsWith($artwork->file_type, 'image/'))
                                    <img src="{{ Storage::url($artwork->file_path) }}"
                                        alt="{{ $artwork->getTitle() }}"
                                        class="file-preview rounded-lg shadow-sm mx-auto">
                                @elseif(Str::startsWith($artwork->file_type, 'video/'))
                                    <video controls class="file-preview rounded-lg shadow-sm mx-auto">
                                        <source src="{{ Storage::url($artwork->file_path) }}"
                                            type="{{ $artwork->file_type }}">
                                    </video>
                                @else
                                    <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">📄</div>
                                            <div class="text-sm text-gray-600">
                                                {{ strtoupper(pathinfo($artwork->file_path, PATHINFO_EXTENSION)) }}
                                                File</div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    <div class="text-4xl mb-2">📄</div>
                                    <p>No file currently attached</p>
                                </div>
                            @endif
                        </div>

                        <!-- Replace File Option -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="replace-file"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="replace-file" class="ml-2 text-sm font-medium text-gray-700">
                                    Replace current file with a new one
                                </label>
                            </div>

                            <!-- New File Upload -->
                            <div id="new-file-section" class="hidden">
                                <div id="drop-zone"
                                    class="drop-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div id="upload-prompt" class="space-y-3">
                                        <div class="text-4xl">🎨</div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">Drop new file here</h3>
                                            <p class="text-gray-500">or <button type="button"
                                                    onclick="document.getElementById('file-input').click()"
                                                    class="text-blue-600 hover:text-blue-700 font-medium">browse
                                                    files</button></p>
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            <p>Supports: Images, Audio, Video, PDF • Max size: 100MB</p>
                                        </div>
                                    </div>

                                    <!-- New File Preview -->
                                    <div id="file-preview" class="hidden space-y-4">
                                        <div id="preview-image" class="flex justify-center">
                                            <!-- New file preview will be inserted here -->
                                        </div>
                                        <div id="file-info" class="text-sm text-gray-600">
                                            <!-- New file info will be inserted here -->
                                        </div>
                                        <button type="button" onclick="clearNewFile()"
                                            class="text-red-600 hover:text-red-700 font-medium">
                                            Remove new file
                                        </button>
                                    </div>

                                    <input type="file" id="file-input" name="file" class="hidden"
                                        accept="image/*,audio/*,video/*,.pdf">
                                </div>
                                @error('file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Artwork Details -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Artwork Details</h2>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Multilingual Fields with Tabs -->
                            <div class="md:col-span-2">
                                <!-- Language Tabs -->
                                <div class="mb-4">
                                    <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                                        @foreach(\App\Models\Language::active()->get() as $index => $language)
                                            <button type="button" 
                                                    class="language-tab flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors {{ $index === 0 ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
                                                    data-language="{{ $language->code }}">
                                                {{ $language->flag_emoji }} {{ $language->native_name }}
                                                @if($language->is_default)
                                                    <span class="text-xs text-blue-500">*</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Language Content Panels -->
                                @foreach(\App\Models\Language::active()->get() as $index => $language)
                                    <div class="language-content {{ $index === 0 ? 'block' : 'hidden' }}" data-language="{{ $language->code }}">
                                        <!-- Title for this language -->
                                        <div class="mb-4">
                                            <label for="title_{{ $language->code }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                Title ({{ $language->native_name }})
                                                @if($language->is_default)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   id="title_{{ $language->code }}" 
                                                   name="title_{{ $language->code }}"
                                                   value="{{ old('title_' . $language->code, $artwork->title[$language->code] ?? '') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                   placeholder="Enter artwork title"
                                                   @if($language->is_default) required @endif>
                                            @error('title_' . $language->code)
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Description for this language -->
                                        <div class="mb-4">
                                            <label for="description_{{ $language->code }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                Description ({{ $language->native_name }})
                                            </label>
                                            <textarea id="description_{{ $language->code }}" 
                                                      name="description_{{ $language->code }}" 
                                                      rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Describe your artwork, inspiration, techniques used...">{{ old('description_' . $language->code, $artwork->description[$language->code] ?? '') }}</textarea>
                                            @error('description_' . $language->code)
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Category -->
                            <div>
                                <label for="category"
                                    class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category" name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->slug }}"
                                            {{ old('category', $artwork->category) == $category->slug ? 'selected' : '' }}>
                                            {{ $category->display_name ?? $category->name['en'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subcategory -->
                            <div>
                                <label for="subcategory"
                                    class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                                <input type="text" id="subcategory" name="subcategory"
                                    value="{{ old('subcategory', $artwork->subcategory) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., Portrait, Landscape">
                                @error('subcategory')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tags -->
                            <div class="md:col-span-2">
                                <label for="tags-input"
                                    class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <input type="text" id="tags-input"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Press Enter to add tags">
                                <div id="tags-container" class="flex flex-wrap gap-2 mt-2">
                                    <!-- Existing tags will be loaded here -->
                                </div>
                                @error('tags')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI & Copyright -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">🤖 AI & Copyright</h2>

                        <div class="space-y-4">
                            <!-- AI Generated -->
                            <div class="flex items-start">
                                <input type="checkbox" id="is_ai_generated" name="is_ai_generated" value="1"
                                    {{ old('is_ai_generated', $artwork->is_ai_generated) ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="ml-3">
                                    <label for="is_ai_generated" class="text-sm font-medium text-gray-700">This is
                                        AI-generated content</label>
                                    <p class="text-sm text-gray-500">Check this if AI tools were used to create this
                                        artwork</p>
                                </div>
                            </div>

                            <!-- AI Tools -->
                            <div id="ai-tools-section" class="{{ $artwork->is_ai_generated ? '' : 'hidden' }}">
                                <label for="ai-tools-input" class="block text-sm font-medium text-gray-700 mb-1">AI
                                    Tools Used</label>
                                <input type="text" id="ai-tools-input"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Press Enter to add AI tools">
                                <div id="ai-tools-container" class="flex flex-wrap gap-2 mt-2">
                                    <!-- Existing AI tools will be loaded here -->
                                </div>
                            </div>

                            <!-- License -->
                            <div>
                                <label for="license_type" class="block text-sm font-medium text-gray-700 mb-1">License
                                    *</label>
                                <select id="license_type" name="license_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach ($licenseTypes as $key => $name)
                                        <option value="{{ $key }}"
                                            {{ old('license_type', $artwork->license_type ?? 'all_rights_reserved') == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('license_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Copyright Notice -->
                            <div>
                                <label for="copyright_notice"
                                    class="block text-sm font-medium text-gray-700 mb-1">Copyright Notice</label>
                                <textarea id="copyright_notice" name="copyright_notice" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="© 2025 Your Name. All rights reserved.">{{ old('copyright_notice', $artwork->copyright_notice) }}</textarea>
                                @error('copyright_notice')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Settings</h2>

                        <div class="space-y-4">
                            <!-- Visibility -->
                            <div>
                                <label for="visibility"
                                    class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                                <select id="visibility" name="visibility" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="public"
                                        {{ old('visibility', $artwork->visibility ?? 'public') == 'public' ? 'selected' : '' }}>
                                        Public - Anyone can see</option>
                                    <option value="unlisted"
                                        {{ old('visibility', $artwork->visibility) == 'unlisted' ? 'selected' : '' }}>
                                        Unlisted - Only with link</option>
                                    <option value="private"
                                        {{ old('visibility', $artwork->visibility) == 'private' ? 'selected' : '' }}>
                                        Private - Only you</option>
                                </select>
                                @error('visibility')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" id="watermark_enabled" name="watermark_enabled"
                                        value="1"
                                        {{ old('watermark_enabled', $artwork->watermark_enabled ?? true) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="watermark_enabled" class="ml-2 text-sm text-gray-700">Enable watermark
                                        protection</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="comments_enabled" name="comments_enabled"
                                        value="1"
                                        {{ old('comments_enabled', $artwork->comments_enabled ?? true) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="comments_enabled" class="ml-2 text-sm text-gray-700">Allow
                                        comments</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="downloads_enabled" name="downloads_enabled"
                                        value="1"
                                        {{ old('downloads_enabled', $artwork->downloads_enabled) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="downloads_enabled" class="ml-2 text-sm text-gray-700">Allow
                                        downloads</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between">
                    <div class="flex space-x-4">
                        <a href="{{ route('artworks.show', $artwork) }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </a>
                    </div>

                    <div class="flex space-x-4">
                        @if ($artwork->status === 'published')
                            <button type="submit" name="action" value="save_draft"
                                class="px-6 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Save as Draft
                            </button>
                        @endif

                        <button type="submit" name="action" value="save" id="save-btn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span id="save-btn-text">Save Changes</span>
                            <span id="save-spinner" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>

                        @if ($artwork->status === 'draft')
                            <button type="submit" name="action" value="publish"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Save & Publish
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Existing data
        const existingTags = @json($artwork->tags ?? []);
        const existingAiTools = @json($artwork->ai_tools_used ?? []);

        let currentTags = [...existingTags];
        let currentAiTools = [...existingAiTools];
        let selectedFile = null;

        // Initialize existing tags
        function initializeExistingData() {
            const tagsContainer = document.getElementById('tags-container');
            const aiToolsContainer = document.getElementById('ai-tools-container');

            existingTags.forEach(tag => {
                addTagElement(tagsContainer, tag, 'currentTags');
            });

            if (existingAiTools.length > 0) {
                existingAiTools.forEach(tool => {
                    addTagElement(aiToolsContainer, tool, 'currentAiTools');
                });
            }

            updateHiddenInputs('currentTags');
            updateHiddenInputs('currentAiTools');
        }

        // File replacement functionality
        document.getElementById('replace-file').addEventListener('change', function() {
            const newFileSection = document.getElementById('new-file-section');
            if (this.checked) {
                newFileSection.classList.remove('hidden');
            } else {
                newFileSection.classList.add('hidden');
                clearNewFile();
            }
        });

        // Drag and drop functionality
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            selectedFile = file;
            displayFilePreview(file);
        }

        function displayFilePreview(file) {
            const uploadPrompt = document.getElementById('upload-prompt');
            const filePreview = document.getElementById('file-preview');
            const previewImage = document.getElementById('preview-image');
            const fileInfo = document.getElementById('file-info');

            uploadPrompt.classList.add('hidden');
            filePreview.classList.remove('hidden');

            // File info
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.innerHTML = `
                <p><strong>New File:</strong> ${file.name}</p>
                <p><strong>Size:</strong> ${sizeInMB} MB</p>
                <p><strong>Type:</strong> ${file.type}</p>
            `;

            // Preview based on file type
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'file-preview rounded-lg shadow-sm';
                previewImage.innerHTML = '';
                previewImage.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.className = 'file-preview rounded-lg shadow-sm';
                previewImage.innerHTML = '';
                previewImage.appendChild(video);
            } else {
                previewImage.innerHTML = `
                    <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-3xl mb-2">📄</div>
                            <div class="text-sm text-gray-600">${file.type.split('/')[1].toUpperCase()}</div>
                        </div>
                    </div>
                `;
            }
        }

        function clearNewFile() {
            selectedFile = null;
            fileInput.value = '';
            document.getElementById('upload-prompt').classList.remove('hidden');
            document.getElementById('file-preview').classList.add('hidden');
            document.getElementById('replace-file').checked = false;
        }

        // Tags functionality
        function setupTagInput(inputId, containerId, arrayName) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);

            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const tag = input.value.trim();
                    if (tag && !window[arrayName].includes(tag)) {
                        window[arrayName].push(tag);
                        addTagElement(container, tag, arrayName);
                        input.value = '';
                        updateHiddenInputs(arrayName);
                    }
                }
            });
        }

        function addTagElement(container, tag, arrayName) {
            const tagElement = document.createElement('span');
            tagElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" onclick="removeTag('${tag}', '${arrayName}', this)" class="ml-2 text-blue-600 hover:text-blue-800">
                    ×
                </button>
            `;
            container.appendChild(tagElement);
        }

        function removeTag(tag, arrayName, element) {
            const index = window[arrayName].indexOf(tag);
            if (index > -1) {
                window[arrayName].splice(index, 1);
                element.parentElement.remove();
                updateHiddenInputs(arrayName);
            }
        }

        function updateHiddenInputs(arrayName) {
            const fieldName = arrayName === 'currentTags' ? 'tags' : 'ai_tools_used';

            // Remove existing hidden inputs
            document.querySelectorAll(`input[name="${fieldName}[]"]`).forEach(input => input.remove());

            // Add new hidden inputs
            window[arrayName].forEach((item, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `${fieldName}[]`;
                input.value = item;
                document.getElementById('artwork-edit-form').appendChild(input);
            });
        }

        // AI generated checkbox functionality
        document.getElementById('is_ai_generated').addEventListener('change', function() {
            const aiToolsSection = document.getElementById('ai-tools-section');
            if (this.checked) {
                aiToolsSection.classList.remove('hidden');
            } else {
                aiToolsSection.classList.add('hidden');
                currentAiTools = [];
                document.getElementById('ai-tools-container').innerHTML = '';
                updateHiddenInputs('currentAiTools');
            }
        });

        // Initialize tag inputs
        setupTagInput('tags-input', 'tags-container', 'currentTags');
        setupTagInput('ai-tools-input', 'ai-tools-container', 'currentAiTools');

        // Form submission
        document.getElementById('artwork-edit-form').addEventListener('submit', function(e) {
            const saveBtn = document.getElementById('save-btn');
            const saveBtnText = document.getElementById('save-btn-text');
            const saveSpinner = document.getElementById('save-spinner');

            if (saveBtn) {
                saveBtn.disabled = true;
                if (saveBtnText) saveBtnText.classList.add('hidden');
                if (saveSpinner) saveSpinner.classList.remove('hidden');
            }
        });

        // Initialize the form with existing data
        initializeExistingData();

        // Language tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const languageTabs = document.querySelectorAll('.language-tab');
            const languageContents = document.querySelectorAll('.language-content');
            
            languageTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetLanguage = this.getAttribute('data-language');
                    
                    // Update tab styles
                    languageTabs.forEach(t => {
                        t.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                        t.classList.add('text-gray-500', 'hover:text-gray-700');
                    });
                    this.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    this.classList.remove('text-gray-500', 'hover:text-gray-700');
                    
                    // Show/hide content panels
                    languageContents.forEach(content => {
                        if (content.getAttribute('data-language') === targetLanguage) {
                            content.classList.remove('hidden');
                            content.classList.add('block');
                        } else {
                            content.classList.add('hidden');
                            content.classList.remove('block');
                        }
                    });
                });
            });
        });

        // Show success/error messages
        @if (session('success'))
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50';
            successDiv.textContent = '✅ {{ session('success') }}';
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 5000);
        @endif

        @if (session('error'))
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50';
            errorDiv.textContent = '❌ {{ session('error') }}';
            document.body.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        @endif
    </script>
</body>

</html>
