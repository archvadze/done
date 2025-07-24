@extends('layouts.admin')

@section('title', 'System Settings')
@section('subtitle', 'Configure platform settings and parameters')

@section('content')
    <div class="space-y-8">
        <!-- Site Configuration -->
        <div class="admin-stats-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Site Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                    <input type="text" value="Acumen Craft" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                    <input type="url" value="https://acumencraft.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" value="contact@acumencraft.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
                    <input type="email" value="support@acumencraft.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- ACQ System Configuration -->
        <div class="admin-stats-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">ACQ System Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Evaluations Required</label>
                    <input type="number" value="3" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Minimum number of evaluations before calculating ACQ score</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto-approve Evaluations</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Automatically approve human evaluations</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Enable AI Evaluations</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Allow AI to evaluate artworks automatically</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Score Calculation Method</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="average" selected>Average</option>
                        <option value="weighted">Weighted Average</option>
                        <option value="median">Median</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="admin-stats-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Management</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registration Enabled</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Verification Required</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default User Role</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="artist" selected>Artist</option>
                        <option value="moderator">Moderator</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Artworks per User</label>
                    <input type="number" value="100" min="1" max="1000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- File Upload Settings -->
        <div class="admin-stats-card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">File Upload Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max File Size (MB)</label>
                    <input type="number" value="50" min="1" max="500" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Allowed File Types</label>
                    <input type="text" value="jpg,jpeg,png,gif,svg,pdf,ai,psd" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Comma-separated list of file extensions</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Optimization</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Generate Thumbnails</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="button" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    onclick="saveSettings()">
                Save Settings
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function saveSettings() {
    alert('Settings save functionality will be implemented soon.');
}
</script>
@endpush
