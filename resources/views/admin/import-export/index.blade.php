<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import & Export') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-200 text-yellow-800 rounded-lg">{{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Import Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import Households
                    </h3>

                    <p class="text-sm text-gray-600 mb-4">Upload an Excel or CSV file to import households in bulk.</p>

                    <a href="{{ route('admin.import-export.template') }}" class="inline-flex items-center text-sm text-teal-600 hover:text-teal-800 mb-4">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Template
                    </a>

                    <form action="{{ route('admin.import-export.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select File</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required>
                            <p class="mt-1 text-xs text-gray-500">Supported: .xlsx, .xls, .csv (max 10MB)</p>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700">Import</button>
                    </form>
                </div>

                <!-- Export Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Data
                    </h3>

                    <div class="space-y-4">
                        <!-- Export Households -->
                        <form action="{{ route('admin.import-export.export-households') }}" method="GET" class="border-b pb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Export Households</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <select name="status" class="text-sm rounded-md border-gray-300">
                                    <option value="">All Status</option>
                                    <option value="verified">Verified</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <select name="region_id" class="text-sm rounded-md border-gray-300">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        @foreach($region->children as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">Export Households</button>
                        </form>

                        <!-- Export Distributions -->
                        <form action="{{ route('admin.import-export.export-distributions') }}" method="GET">
                            <p class="text-sm font-medium text-gray-700 mb-2">Export Distributions</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input type="date" name="from_date" class="text-sm rounded-md border-gray-300" placeholder="From">
                                <input type="date" name="to_date" class="text-sm rounded-md border-gray-300" placeholder="To">
                            </div>
                            <button type="submit" class="w-full py-2 px-4 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">Export Distributions</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Imports -->
            <div class="mt-6 bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="font-medium text-gray-900">Recent Imports</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentImports as $import)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $import->file_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $import->created_at->format('M j, H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $import->user->name ?? 'System' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($import->status === 'completed') bg-green-100 text-green-800
                                        @elseif($import->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($import->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($import->status === 'completed')
                                        {{ $import->rows_processed }} ok, {{ $import->rows_failed }} failed
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No imports yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
