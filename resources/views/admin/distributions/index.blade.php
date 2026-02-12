<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('messages.distributions.title') }}</h2>
            <a href="{{ route('admin.distributions.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('messages.distributions.record') }}
            </a>
        </div>
    </x-slot>
    <style>
        .flex{
            gap:5px;
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('messages.distributions.search_placeholder') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    </div>
                    <div>
                        <select name="program_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">{{ __('messages.distributions.all_programs') }}</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ ($filters['program_id'] ?? '') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.distributions.from_date') }}">
                    </div>
                    <div>
                        <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.distributions.to_date') }}">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-teal-700">{{ __('messages.actions.filter') }}</button>
                        <a href="{{ route('admin.distributions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('messages.actions.clear') }}</a>
                    </div>
                </form>
            </div>

            <!-- Results -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.distributions.table.household') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.distributions.table.program') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.distributions.table.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('messages.distributions.table.recorded_by') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('messages.distributions.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($distributions as $dist)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.households.show', $dist->household_id) }}" class="font-medium text-gray-900 hover:text-teal-600">{{ $dist->household->head_name ?? __('messages.general.unknown') }}</a>
                                    <p class="text-sm text-gray-500">{{ $dist->household->head_national_id ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dist->aidProgram->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $dist->distribution_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $dist->distributor->name ?? __('messages.general.system') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.distributions.destroy', $dist) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('messages.distributions.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">{{ __('messages.distributions.no_results') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($distributions->hasPages())
                    <div class="px-6 py-4 border-t">{{ $distributions->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
