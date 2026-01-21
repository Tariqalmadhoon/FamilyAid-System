<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <!-- Total Households -->
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-teal-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Households</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_households']) }}</p>
                        </div>
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Verification -->
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_households']) }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Programs -->
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Active Programs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_programs']) }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- This Month Distributions -->
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">This Month</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month_distributions']) }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('admin.households.create') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-teal-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Add Household</span>
                </a>
                <a href="{{ route('admin.distributions.create') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Record Distribution</span>
                </a>
                <a href="{{ route('admin.programs.create') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">New Program</span>
                </a>
                <a href="{{ route('admin.households.index', ['status' => 'pending']) }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Verify Pending</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Households -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">Recent Households</h3>
                        <a href="{{ route('admin.households.index') }}" class="text-sm text-teal-600 hover:text-teal-800">View All</a>
                    </div>
                    <div class="divide-y">
                        @forelse($recentHouseholds as $household)
                            <a href="{{ route('admin.households.show', $household) }}" class="block p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $household->head_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $household->head_national_id }} • {{ $household->region->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($household->status === 'verified') bg-green-100 text-green-800
                                        @elseif($household->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($household->status) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-gray-500">No households yet</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Distributions -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">Recent Distributions</h3>
                        <a href="{{ route('admin.distributions.index') }}" class="text-sm text-teal-600 hover:text-teal-800">View All</a>
                    </div>
                    <div class="divide-y">
                        @forelse($recentDistributions as $dist)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $dist->household->head_name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-gray-500">{{ $dist->aidProgram->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $dist->distribution_date->format('M j') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">No distributions yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
