<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.programs.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $program->name }}</h2>
            </div>
            @if($program->is_active)
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500">Total Distributions</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($program->distributions_count) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
                    <p class="text-sm text-gray-500 mb-2">Description</p>
                    <p class="text-gray-700">{{ $program->description ?? 'No description' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">Distribution Records</h3>
                    <a href="{{ route('admin.distributions.create') }}" class="text-sm text-teal-600 hover:text-teal-800">+ Record New</a>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Household</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($distributions as $dist)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.households.show', $dist->household_id) }}" class="font-medium text-gray-900 hover:text-teal-600">{{ $dist->household->head_name ?? 'Unknown' }}</a>
                                    <p class="text-sm text-gray-500">{{ $dist->household->head_national_id ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $dist->distribution_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $dist->distributor->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">No distributions yet</td></tr>
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
