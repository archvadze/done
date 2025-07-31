@extends('layouts.app')

@section('title', __('Create Support Ticket'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Create Support Ticket') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Describe your issue in detail and our support team will assist you.') }}</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Subject') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="subject" 
                           id="subject" 
                           value="{{ old('subject') }}" 
                           required
                           placeholder="{{ __('Brief description of your issue...') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('subject') border-red-500 @enderror">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Category') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('category') border-red-500 @enderror">
                        <option value="">{{ __('Select a category...') }}</option>
                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>{{ __('General Question') }}</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>{{ __('Technical Issue') }}</option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>{{ __('Account Problem') }}</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>{{ __('Billing & Payments') }}</option>
                        <option value="community" {{ old('category') === 'community' ? 'selected' : '' }}>{{ __('Community Issues') }}</option>
                        <option value="artwork" {{ old('category') === 'artwork' ? 'selected' : '' }}>{{ __('Artwork & Content') }}</option>
                        <option value="bug" {{ old('category') === 'bug' ? 'selected' : '' }}>{{ __('Bug Report') }}</option>
                        <option value="feature" {{ old('category') === 'feature' ? 'selected' : '' }}>{{ __('Feature Request') }}</option>
                        <option value="abuse" {{ old('category') === 'abuse' ? 'selected' : '' }}>{{ __('Report Abuse') }}</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Priority') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('priority') border-red-500 @enderror">
                        <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>{{ __('Low - General inquiry') }}</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>{{ __('Normal - Standard support') }}</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('High - Account issue') }}</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>{{ __('Urgent - Service down') }}</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Higher priority tickets receive faster response times.') }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Description') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="8" 
                              required
                              placeholder="{{ __('Please provide a detailed description of your issue or question. Include any relevant information such as error messages, steps to reproduce the problem, or specific questions you have.') }}"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('The more details you provide, the better we can help you.') }}</p>
                </div>

                <!-- Steps to Reproduce (for bug reports) -->
                <div id="steps-section" class="hidden">
                    <label for="steps" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Steps to Reproduce') }}
                    </label>
                    <textarea name="steps" 
                              id="steps" 
                              rows="4" 
                              placeholder="{{ __('If this is a bug report, please list the steps to reproduce the issue:') }}&#10;1. &#10;2. &#10;3. "
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('steps') border-red-500 @enderror">{{ old('steps') }}</textarea>
                    @error('steps')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected vs Actual Behavior (for bug reports) -->
                <div id="behavior-section" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="expected_behavior" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Expected Behavior') }}
                            </label>
                            <textarea name="expected_behavior" 
                                      id="expected_behavior" 
                                      rows="3" 
                                      placeholder="{{ __('What did you expect to happen?') }}"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('expected_behavior') border-red-500 @enderror">{{ old('expected_behavior') }}</textarea>
                            @error('expected_behavior')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="actual_behavior" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Actual Behavior') }}
                            </label>
                            <textarea name="actual_behavior" 
                                      id="actual_behavior" 
                                      rows="3" 
                                      placeholder="{{ __('What actually happened?') }}"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('actual_behavior') border-red-500 @enderror">{{ old('actual_behavior') }}</textarea>
                            @error('actual_behavior')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('System Information') }}
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="browser" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('Browser') }}
                            </label>
                            <input type="text" 
                                   name="browser" 
                                   id="browser" 
                                   value="{{ old('browser') }}" 
                                   placeholder="{{ __('e.g., Chrome 91, Firefox 89, Safari 14') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('browser') border-red-500 @enderror">
                            @error('browser')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="operating_system" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                {{ __('Operating System') }}
                            </label>
                            <input type="text" 
                                   name="operating_system" 
                                   id="operating_system" 
                                   value="{{ old('operating_system') }}" 
                                   placeholder="{{ __('e.g., Windows 11, macOS Big Sur, Ubuntu 20.04') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('operating_system') border-red-500 @enderror">
                            @error('operating_system')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('System information helps us diagnose technical issues faster.') }}</p>
                </div>

                <!-- File Attachments -->
                <div>
                    <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Attachments') }}
                    </label>
                    <input type="file" 
                           name="attachments[]" 
                           id="attachments" 
                           multiple
                           accept="image/*,.pdf,.doc,.docx,.txt,.log"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('attachments') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Upload screenshots, error logs, or other relevant files. Max 5MB per file, up to 3 files.') }}
                        <br>
                        <span class="text-xs">{{ __('Supported formats: images, PDF, Word documents, text files, log files') }}</span>
                    </p>
                    @error('attachments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Preference -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Preferred Contact Method') }}
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="contact_preference" 
                                   value="email" 
                                   {{ old('contact_preference', 'email') === 'email' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Email updates (recommended)') }}
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="contact_preference" 
                                   value="ticket" 
                                   {{ old('contact_preference') === 'ticket' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Only through ticket system') }}
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-ticket-alt mr-2"></i>{{ __('Create Ticket') }}
                    </button>
                    <a href="{{ route('support.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-medium text-center transition-colors">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>{{ __('Before Creating a Ticket') }}
                </h3>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                        <span>{{ __('Search our FAQ for quick answers') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                        <span>{{ __('Check if the issue is mentioned in our help articles') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                        <span>{{ __('Include as much detail as possible') }}</span>
                    </li>
                </ul>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('support.faq.index') }}" class="block text-blue-500 hover:text-blue-600 text-sm">
                        <i class="fas fa-arrow-right mr-2"></i>{{ __('Browse FAQ') }}
                    </a>
                    <a href="{{ route('support.help.index') }}" class="block text-blue-500 hover:text-blue-600 text-sm">
                        <i class="fas fa-arrow-right mr-2"></i>{{ __('View Help Articles') }}
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-clock text-blue-500 mr-2"></i>{{ __('Response Times') }}
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Low Priority') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ __('2-3 business days') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Normal Priority') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ __('1-2 business days') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('High Priority') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ __('4-8 hours') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Urgent') }}:</span>
                        <span class="text-gray-900 dark:text-white">{{ __('1-2 hours') }}</span>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Business hours: Monday-Friday, 9 AM - 5 PM EST') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const stepsSection = document.getElementById('steps-section');
    const behaviorSection = document.getElementById('behavior-section');
    
    function toggleBugReportFields() {
        const isBugReport = categorySelect.value === 'bug';
        
        if (isBugReport) {
            stepsSection.classList.remove('hidden');
            behaviorSection.classList.remove('hidden');
        } else {
            stepsSection.classList.add('hidden');
            behaviorSection.classList.add('hidden');
        }
    }
    
    // Initialize on page load
    toggleBugReportFields();
    
    // Toggle when category changes
    categorySelect.addEventListener('change', toggleBugReportFields);
    
    // Auto-detect browser and OS
    if (navigator.userAgent) {
        const browserField = document.getElementById('browser');
        const osField = document.getElementById('operating_system');
        
        // Simple browser detection
        let browser = 'Unknown';
        if (navigator.userAgent.includes('Chrome')) browser = 'Chrome';
        else if (navigator.userAgent.includes('Firefox')) browser = 'Firefox';
        else if (navigator.userAgent.includes('Safari')) browser = 'Safari';
        else if (navigator.userAgent.includes('Edge')) browser = 'Edge';
        
        // Simple OS detection
        let os = 'Unknown';
        if (navigator.userAgent.includes('Windows')) os = 'Windows';
        else if (navigator.userAgent.includes('Mac')) os = 'macOS';
        else if (navigator.userAgent.includes('Linux')) os = 'Linux';
        else if (navigator.userAgent.includes('Android')) os = 'Android';
        else if (navigator.userAgent.includes('iOS')) os = 'iOS';
        
        if (browserField && !browserField.value) {
            browserField.value = browser;
        }
        if (osField && !osField.value) {
            osField.value = os;
        }
    }
});
</script>
@endpush