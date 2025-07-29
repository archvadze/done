@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="text-gray-400 mb-4">
                <a href="{{ route('support.index') }}" class="hover:text-white">Support</a>
                <span class="mx-2">/</span>
                <a href="{{ route('support.tickets.index') }}" class="hover:text-white">My Tickets</a>
                <span class="mx-2">/</span>
                <span class="text-white">Create Ticket</span>
            </nav>
            
            <h1 class="text-4xl font-bold text-secondary mb-4">Create Support Ticket</h1>
            <p class="text-white">Describe your issue and we'll help you resolve it</p>
        </div>

        <!-- Create Ticket Form -->
        <div class="bg-secondary p-8">
            <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Subject -->
                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-white mb-2">Subject *</label>
                    <input type="text" 
                           name="subject" 
                           id="subject" 
                           value="{{ old('subject') }}" 
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 bg-primary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary @error('subject') border-red-500 @enderror"
                           placeholder="Brief description of your issue">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label for="category" class="block text-sm font-medium text-white mb-2">Category *</label>
                    <select name="category" id="category" required 
                            class="w-full px-3 py-2 bg-primary text-white focus:outline-none focus:ring-2 focus:ring-secondary @error('category') border-red-500 @enderror">
                        <option value="">Select a category...</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Issue</option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Account Issue</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing & Payments</option>
                        <option value="content" {{ old('category') === 'content' ? 'selected' : '' }}>Content & Uploads</option>
                        <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                        <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-white mb-2">Priority *</label>
                    <select name="priority" id="priority" required 
                            class="w-full px-3 py-2 bg-primary text-white focus:outline-none focus:ring-2 focus:ring-secondary @error('priority') border-red-500 @enderror">
                        <option value="">Select priority...</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low - General question</option>
                        <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }} selected>Normal - Standard issue</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High - Affects functionality</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent - Critical issue</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-white mb-2">Description *</label>
                    <textarea name="description" 
                              id="description" 
                              rows="8" 
                              required
                              maxlength="5000"
                              class="w-full px-3 py-2 bg-primary text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary @error('description') border-red-500 @enderror"
                              placeholder="Please provide detailed information about your issue including:&#10;- What happened?&#10;- What were you trying to do?&#10;- What did you expect to happen?&#10;- Any error messages you received&#10;- Steps to reproduce the issue">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-400">
                        <span id="char-count">0</span>/5,000 characters
                    </div>
                </div>

                <!-- Attachments -->
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-white mb-2">Attachments (Optional)</label>
                    <input type="file" 
                           name="attachments[]" 
                           id="attachments" 
                           multiple
                           accept="image/*,.pdf,.doc,.docx,.txt,.log"
                           class="w-full px-3 py-2 bg-primary text-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-secondary file:text-white hover:file:bg-opacity-80">
                    <p class="mt-1 text-sm text-gray-400">
                        Upload screenshots, error logs, or documents. Max 5 files, 10MB each.
                    </p>
                    @error('attachments')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Support Tips -->
                <div class="mb-6 p-4 bg-primary bg-opacity-50">
                    <h3 class="text-white font-semibold mb-2">💡 Tips for Faster Resolution</h3>
                    <ul class="text-gray-300 text-sm space-y-1">
                        <li>• Include specific error messages or screenshots</li>
                        <li>• Mention your browser and device type</li>
                        <li>• Describe the exact steps that led to the issue</li>
                        <li>• Check our <a href="{{ route('support.faq.index') }}" class="text-secondary hover:underline">FAQ</a> first for quick answers</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('support.tickets.index') }}" 
                       class="px-6 py-2 bg-gray-600 text-white hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    
                    <button type="submit" class="btn-primary px-8 py-2">
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>

        <!-- Additional Help -->
        <div class="mt-8 bg-secondary p-6">
            <h3 class="text-white font-semibold mb-3">Need Immediate Help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <h4 class="text-secondary font-medium mb-1">Check FAQs</h4>
                    <p class="text-gray-300">Common questions and quick solutions</p>
                    <a href="{{ route('support.faq.index') }}" class="text-secondary hover:underline">Browse FAQ →</a>
                </div>
                <div>
                    <h4 class="text-secondary font-medium mb-1">Help Articles</h4>
                    <p class="text-gray-300">Detailed guides and tutorials</p>
                    <a href="{{ route('support.help.index') }}" class="text-secondary hover:underline">Read Guides →</a>
                </div>
                <div>
                    <h4 class="text-secondary font-medium mb-1">Community</h4>
                    <p class="text-gray-300">Ask questions in our community</p>
                    <a href="{{ route('communities.index') }}" class="text-secondary hover:underline">Join Discussion →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
document.getElementById('description').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('char-count').textContent = charCount;
    
    if (charCount > 4500) {
        document.getElementById('char-count').classList.add('text-yellow-400');
    } else {
        document.getElementById('char-count').classList.remove('text-yellow-400');
    }
    
    if (charCount > 5000) {
        document.getElementById('char-count').classList.add('text-red-400');
        document.getElementById('char-count').classList.remove('text-yellow-400');
    } else {
        document.getElementById('char-count').classList.remove('text-red-400');
    }
});
</script>
@endsection
