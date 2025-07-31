@extends('layouts.app')

@section('title', __('Contact Support'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Contact Support') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Get in touch with our support team. We\'ll get back to you as soon as possible.') }}</p>
                </div>

                @if (session('success'))
                    <div
                        class="bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('support.contact.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Your Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name', auth()->user()->name ?? '') }}" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Email Address') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', auth()->user()->email ?? '') }}" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Subject') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="subject" id="subject" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('subject') border-red-500 @enderror">
                            <option value="">{{ __('Select a topic...') }}</option>
                            <option value="general" {{ old('subject') === 'general' ? 'selected' : '' }}>
                                {{ __('General Question') }}</option>
                            <option value="technical" {{ old('subject') === 'technical' ? 'selected' : '' }}>
                                {{ __('Technical Issue') }}</option>
                            <option value="account" {{ old('subject') === 'account' ? 'selected' : '' }}>
                                {{ __('Account Problem') }}</option>
                            <option value="billing" {{ old('subject') === 'billing' ? 'selected' : '' }}>
                                {{ __('Billing & Payments') }}</option>
                            <option value="community" {{ old('subject') === 'community' ? 'selected' : '' }}>
                                {{ __('Community Issues') }}</option>
                            <option value="artwork" {{ old('subject') === 'artwork' ? 'selected' : '' }}>
                                {{ __('Artwork & Content') }}</option>
                            <option value="bug" {{ old('subject') === 'bug' ? 'selected' : '' }}>{{ __('Bug Report') }}
                            </option>
                            <option value="feature" {{ old('subject') === 'feature' ? 'selected' : '' }}>
                                {{ __('Feature Request') }}</option>
                            <option value="other" {{ old('subject') === 'other' ? 'selected' : '' }}>{{ __('Other') }}
                            </option>
                        </select>
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Priority') }}
                        </label>
                        <select name="priority" id="priority"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('priority') border-red-500 @enderror">
                            <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>
                                {{ __('Low - General inquiry') }}</option>
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>
                                {{ __('Normal - Standard support') }}</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                {{ __('High - Account issue') }}</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                {{ __('Urgent - Service down') }}</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Message') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" required
                            placeholder="{{ __('Please describe your issue or question in detail...') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Please provide as much detail as possible to help us assist you better.') }}</p>
                    </div>

                    <!-- File Attachment -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Attachment (Optional)') }}
                        </label>
                        <input type="file" name="attachment" id="attachment" accept="image/*,.pdf,.doc,.docx,.txt"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('attachment') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Max 5MB. Supported formats: images, PDF, Word documents, text files') }}</p>
                        @error('attachment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg font-medium transition-colors flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Message') }}
                        </button>
                        <a href="{{ route('support.index') }}"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-medium text-center transition-colors">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Additional Support Options -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-question-circle text-blue-500 mr-2"></i>{{ __('Before You Contact Us') }}
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ __('Check our FAQ for quick answers') }}</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ __('Browse help articles for detailed guides') }}</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ __('Try clearing your browser cache') }}</span>
                        </li>
                    </ul>
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('support.faq.index') }}" class="block text-blue-500 hover:text-blue-600 text-sm">
                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Visit FAQ') }}
                        </a>
                        <a href="{{ route('support.help.index') }}"
                            class="block text-blue-500 hover:text-blue-600 text-sm">
                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Browse Help Articles') }}
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-clock text-green-500 mr-2"></i>{{ __('Response Times') }}
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
                        {{ __('Response times are during business hours (Monday-Friday, 9 AM - 5 PM EST)') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
