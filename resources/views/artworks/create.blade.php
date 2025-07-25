<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>>>
    <meta charset>="utf-8">>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <titl>e>Upload Artwork - Acumen Craft</title>
        >
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

            .progress-bar {
                transition: width 0.3s ease;
            }
        </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">
                            🎨 Acumen Craft
                        </a>
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Upload Your Artwork</h1>
                <p class="text-gray-600">Share your creative work with the Acumen Craft community</p>
            </div>

            <form id="artwork-upload-form" method="POST" action="{{ route('artworks.store') }}"
                enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- File Upload Area -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📁 Select Your File</h2>

                        <!-- Drop Zone -->
                        <div id="drop-zone"
                            class="drop-zone relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors">
                            <div id="upload-prompt" class="space-y-4">
                                <div class="text-6xl">🎨</div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Drop your artwork here</h3>
                                    <p class="text-gray-500">or <button type="button"
                                            onclick="document.getElementById('file-input').click()"
                                            class="text-blue-600 hover:text-blue-700 font-medium">browse files</button>
                                    </p>
                                </div>
                                <div class="text-sm text-gray-400">
                                    <p>Supports: Images, Audio, Video, PDF</p>
                                    <p>Max size: 100MB</p>
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div id="file-preview" class="hidden space-y-4">
                                <div id="preview-image" class="flex justify-center">
                                    <!-- Image/video preview will be inserted here -->
                                </div>
                                <div id="file-info" class="text-sm text-gray-600">
                                    <!-- File info will be inserted here -->
                                </div>
                                <button type="button" onclick="clearFile()"
                                    class="text-red-600 hover:text-red-700 font-medium">
                                    Remove file
                                </button>
                            </div>

                            <!-- Upload Progress -->
                            <div id="upload-progress" class="hidden mt-4">
                                <div class="bg-gray-200 rounded-full h-3">
                                    <div class="progress-bar bg-blue-600 h-3 rounded-full" style="width: 0%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    <span id="progress-text">Uploading...</span>
                                    <span id="progress-percent">0%</span>
                                </p>
                            </div>

                            <input type="file" id="file-input" name="file" class="hidden"
                                accept="image/*,audio/*,video/*,.pdf" required>
                        </div>

                        @error('file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Artwork Details -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Artwork Details</h2>

                        <!-- Simple Title and Description -->
                        <div class="grid grid-cols-1 gap-6 mb-8">
                            <!-- Auto Language Detection Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800">🌍 Automatic Language Detection
                                        </h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Just write naturally! The system will automatically detect your language and
                                            translate to all active languages.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Title with Auto-Detection -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Title <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500">(Language will be auto-detected)</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Enter artwork title in any language (Georgian, German, English)" 
                                    onchange="detectLanguage(this, 'title-detection')" required>
                                <div id="title-detection" class="mt-1 text-xs text-gray-600"></div>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description with Auto-Detection -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                    <span class="text-xs text-gray-500">(Language will be auto-detected)</span>
                                </label>
                                <textarea id="description" name="description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Describe your artwork in any language (Georgian, German, English)..."
                                    onchange="detectLanguage(this, 'desc-detection')">{{ old('description') }}</textarea>
                                <div id="desc-detection" class="mt-1 text-xs text-gray-600"></div>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-6 border-t border-gray-200">
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category
                                    <span class="text-red-500">*</span></label>
                                <select id="category" name="category" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->slug }}"
                                            {{ old('category') == $category->slug ? 'selected' : '' }}>
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
                                    value="{{ old('subcategory') }}"
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
                                    placeholder="Press Enter to add tags (e.g., art, digital, painting)">
                                <div id="tags-container" class="flex flex-wrap gap-2 mt-2">
                                    <!-- Tags will be added here dynamically -->
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
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ old('is_ai_generated') ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <label for="is_ai_generated" class="text-sm font-medium text-gray-700">This is
                                        AI-generated content</label>
                                    <p class="text-sm text-gray-500">Check this if AI tools were used to create this
                                        artwork</p>
                                </div>
                            </div>

                            <!-- AI Tools -->
                            <div id="ai-tools-section" class="hidden">
                                <label for="ai-tools-input" class="block text-sm font-medium text-gray-700 mb-1">AI
                                    Tools Used</label>
                                <input type="text" id="ai-tools-input"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Press Enter to add AI tools (e.g., DALL-E, Midjourney, ChatGPT)">
                                <div id="ai-tools-container" class="flex flex-wrap gap-2 mt-2">
                                    <!-- AI tools will be added here dynamically -->
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
                                            {{ old('license_type', 'all_rights_reserved') == $key ? 'selected' : '' }}>
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
                                    placeholder="© 2025 Your Name. All rights reserved.">{{ old('copyright_notice') }}</textarea>
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
                                        {{ old('visibility', 'public') == 'public' ? 'selected' : '' }}>Public - Anyone
                                        can see</option>
                                    <option value="unlisted" {{ old('visibility') == 'unlisted' ? 'selected' : '' }}>
                                        Unlisted - Only with link</option>
                                    <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>
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
                                        value="1" checked
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="watermark_enabled" class="ml-2 text-sm text-gray-700">Enable watermark
                                        protection</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="comments_enabled" name="comments_enabled"
                                        value="1" checked
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="comments_enabled" class="ml-2 text-sm text-gray-700">Allow
                                        comments</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="downloads_enabled" name="downloads_enabled"
                                        value="1"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="downloads_enabled" class="ml-2 text-sm text-gray-700">Allow
                                        downloads</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="window.history.back()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" id="upload-btn"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span id="upload-btn-text">Upload Artwork</span>
                        <span id="upload-spinner" class="hidden">
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
                </div>
            </form>
        </div>
    </div>

    <script>
        // File upload handling
        let selectedFile = null;
        let currentTags = [];
        let currentAiTools = [];

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

            // Auto-populate title if empty
            const titleInput = document.getElementById('title');
            if (!titleInput.value) {
                const fileName = file.name.split('.').slice(0, -1).join('.');
                titleInput.value = fileName.charAt(0).toUpperCase() + fileName.slice(1);
            }
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
                <p><strong>File:</strong> ${file.name}</p>
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

        function clearFile() {
            selectedFile = null;
            fileInput.value = '';
            document.getElementById('upload-prompt').classList.remove('hidden');
            document.getElementById('file-preview').classList.add('hidden');
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
                document.getElementById('artwork-upload-form').appendChild(input);
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
        document.getElementById('artwork-upload-form').addEventListener('submit', function(e) {
            const uploadBtn = document.getElementById('upload-btn');
            const uploadBtnText = document.getElementById('upload-btn-text');
            const uploadSpinner = document.getElementById('upload-spinner');

            uploadBtn.disabled = true;
            uploadBtnText.classList.add('hidden');
            uploadSpinner.classList.remove('hidden');

            // Show upload progress
            document.getElementById('upload-progress').classList.remove('hidden');

            // Simulate progress for now - in real implementation, you'd track actual upload progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 95) {
                    clearInterval(progressInterval);
                    progress = 100;
                }

                document.querySelector('.progress-bar').style.width = progress + '%';
                document.getElementById('progress-percent').textContent = Math.round(progress) + '%';

                if (progress >= 100) {
                    document.getElementById('progress-text').textContent = 'Processing...';
                }
            }, 500);
        });

        // Show success message if redirected back
        @if (session('success'))
            alert('✅ {{ session('success') }}');
        @endif

        @if (session('error'))
            alert('❌ {{ session('error') }}');
        @endif

        // Language Detection Function
        async function detectLanguage(element, targetId) {
            const text = element.value.trim();
            if (text.length < 3) {
                document.getElementById(targetId).innerHTML = '';
                return;
            }

            try {
                // Simple client-side detection
                let detectedLanguage = 'en'; // default
                let languageName = 'English';
                
                // Georgian detection
                if (/[ა-ჿ]/.test(text)) {
                    detectedLanguage = 'ka';
                    languageName = 'Georgian';
                }
                // German detection
                else if (/[äöüßÄÖÜ]/.test(text)) {
                    detectedLanguage = 'de';
                    languageName = 'German';
                }

                document.getElementById(targetId).innerHTML = 
                    `🌐 Detected language: <strong>${languageName}</strong> (${detectedLanguage})`;
                
                // Store detected language in hidden field if needed
                let hiddenField = document.getElementById('detected_language');
                if (!hiddenField) {
                    hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'detected_language';
                    hiddenField.id = 'detected_language';
                    element.form.appendChild(hiddenField);
                }
                hiddenField.value = detectedLanguage;

            } catch (error) {
                console.error('Language detection error:', error);
                document.getElementById(targetId).innerHTML = 
                    '<span class="text-red-500">Detection failed</span>';
            }
        }
        @endif
    </script>
</body>

</html>
