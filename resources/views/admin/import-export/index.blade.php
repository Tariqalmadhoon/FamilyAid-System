<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('messages.import_export.title') }}</h2>
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
                        {{ __('messages.import_export.import_households') }}
                    </h3>

                    <p class="text-sm text-gray-600 mb-4">{{ __('messages.import_export.import_description') }}</p>

                    <a href="{{ route('admin.import-export.template') }}" class="inline-flex items-center text-sm text-teal-600 hover:text-teal-800 mb-4">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ __('messages.import_export.download_template') }}
                    </a>

                    <form action="{{ route('admin.import-export.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.import_export.select_file') }}</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required>
                            <p class="mt-1 text-xs text-gray-500">{{ __('messages.import_export.supported_formats') }}</p>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700">{{ __('messages.import_export.import_btn') }}</button>
                    </form>
                </div>

                <!-- Export Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ __('messages.import_export.export_data') }}
                    </h3>

                    <div class="space-y-4">
                        <!-- Export Households -->
                        <form action="/direct-export.php" method="GET" class="border-b pb-4">
                            <input type="hidden" name="type" value="households">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('messages.import_export.export_households') }}</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <select name="status" class="text-sm rounded-md border-gray-300">
                                    <option value="">{{ __('messages.import_export.all_status') }}</option>
                                    <option value="verified">{{ __('messages.status.verified') }}</option>
                                    <option value="pending">{{ __('messages.status.pending') }}</option>
                                </select>
                                <select name="region_id" class="text-sm rounded-md border-gray-300">
                                    <option value="">{{ __('messages.import_export.all_regions') }}</option>
                                    @foreach($regions as $region)
                                        @foreach($region->children as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">{{ __('messages.import_export.export_households') }}</button>
                        </form>

                        <!-- Export Distributions -->
                        <form action="/direct-export.php" method="GET">
                            <input type="hidden" name="type" value="distributions">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('messages.import_export.export_distributions') }}</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input type="date" name="from_date" class="text-sm rounded-md border-gray-300" placeholder="{{ __('messages.import_export.from_date') }}">
                                <input type="date" name="to_date" class="text-sm rounded-md border-gray-300" placeholder="{{ __('messages.import_export.to_date') }}">
                            </div>
                            <button type="submit" class="w-full py-2 px-4 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">{{ __('messages.import_export.export_distributions') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Imports -->
            <div class="mt-6 bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="font-medium text-gray-900">{{ __('messages.import_export.recent_imports') }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.import_export.file') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.import_export.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.import_export.user') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.import_export.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.import_export.result') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentImports as $import)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $import->file_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $import->created_at->format('M j, H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $import->user->name ?? __('messages.general.unknown') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($import->status === 'completed') bg-green-100 text-green-800
                                        @elseif($import->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ __('messages.status.' . $import->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($import->status === 'completed')
                                        {{ __('messages.import_export.result_ok_failed', ['ok' => $import->success_count, 'failed' => $import->error_count]) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('messages.import_export.no_imports') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
