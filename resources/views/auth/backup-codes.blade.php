<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Recovery Codes - Acumen Craft</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Recovery Backup Codes</h1>
                <p class="text-gray-600">Save these codes in a secure location</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Important Security Information
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Each backup code can only be used once</li>
                                <li>Store these codes in a secure password manager</li>
                                <li>Don't share these codes with anyone</li>
                                <li>Generate new codes if you suspect they've been compromised</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recovery Codes -->
            <div class="mb-8">
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($codes as $code)
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-3 text-center">
                            <code class="text-lg font-mono text-gray-800 select-all">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="downloadCodes()"
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download as Text
                </button>

                <button onclick="printCodes()"
                    class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Codes
                </button>

                <a href="{{ route('two-factor.backup-codes') }}"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 text-center">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Generate New Codes
                </a>
            </div>

            <!-- Navigation -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <a href="{{ route('two-factor.show') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Two-Factor Settings
                </a>
            </div>
        </div>
    </div>

    <script>
        function downloadCodes() {
            const codes = @json($codes);
            const content = "Acumen Craft - Recovery Backup Codes\n" +
                "Generated: " + new Date().toLocaleDateString() + "\n" +
                "======================================\n\n" +
                codes.join('\n') +
                "\n\n** IMPORTANT **\n" +
                "- Each code can only be used once\n" +
                "- Store securely and don't share\n" +
                "- Generate new codes if compromised";

            const blob = new Blob([content], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'acumen-craft-backup-codes.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        function printCodes() {
            const codes = @json($codes);
            const printContent = `
                <html>
                <head>
                    <title>Acumen Craft - Recovery Backup Codes</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .codes { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0; }
                        .code { border: 1px solid #ccc; padding: 10px; text-align: center; font-family: monospace; font-size: 16px; }
                        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Acumen Craft</h1>
                        <h2>Recovery Backup Codes</h2>
                        <p>Generated: ${new Date().toLocaleDateString()}</p>
                    </div>
                    <div class="codes">
                        ${codes.map(code => `<div class="code">${code}</div>`).join('')}
                    </div>
                    <div class="warning">
                        <strong>IMPORTANT:</strong>
                        <ul>
                            <li>Each backup code can only be used once</li>
                            <li>Store these codes in a secure location</li>
                            <li>Don't share these codes with anyone</li>
                            <li>Generate new codes if you suspect they've been compromised</li>
                        </ul>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>

</html>
