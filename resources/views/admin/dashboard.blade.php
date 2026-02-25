<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <style>
        .p-2{
            margin: 10px
        }
    </style>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <!-- Total Households -->
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-teal-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Total Households') }}</p>
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
                            <p class="text-sm font-medium text-gray-500">{{ __('messages.status.pending') }}</p>
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
                            <p class="text-sm font-medium text-gray-500">{{ __('Active Programs') }}</p>
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
                            <p class="text-sm font-medium text-gray-500">{{ __('messages.this_month') }}</p>
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

            <!-- Camp Statistics -->
            <div class="mb-8" x-data="{ open: false }">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button
                        type="button"
                        @click="open = !open"
                        class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition"
                    >
                        <div class="text-right">
                            <p class="text-base font-semibold text-gray-900">إحصائيات المخيمات</p>
                            <p class="text-sm text-gray-500">عدد الأسر المسجلة في كل مخيم</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                                {{ number_format($totalCampRegistered) }} مسجل
                            </span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                :class="{ 'rotate-180': open }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="border-t border-gray-100"
                    >
                        @if($campStats->isEmpty())
                            <div class="p-6 text-center text-gray-500">لا توجد مخيمات متاحة حالياً.</div>
                        @else
                            <div class="p-4 sm:p-5 space-y-3">
                                @foreach($campStats as $camp)
                                    @php
                                        $count = (int) $camp->households_count;
                                        $percent = (int) round(($count / $maxCampRegistered) * 100);
                                    @endphp
                                    <a href="{{ route('admin.households.index', ['region_id' => $camp->id]) }}" class="block rounded-lg border border-gray-100 p-3 hover:border-teal-200 hover:bg-teal-50/40 transition">
                                        <div class="flex items-center justify-between gap-3 mb-2">
                                            <p class="font-medium text-gray-800">{{ $camp->name }}</p>
                                            <span class="text-sm font-semibold text-teal-700">{{ number_format($count) }}</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ $percent }}%;"></div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
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
                    <span class="text-sm font-medium text-gray-700">{{ __('Add Household') }}</span>
                </a>
                <a href="{{ route('admin.distributions.create') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('Record Distribution') }}</span>
                </a>
                <a href="{{ route('admin.programs.create') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('New Program') }}</span>
                </a>
                <a href="{{ route('admin.households.index', ['status' => 'pending']) }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ __('Verify Pending') }}</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Households -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">{{ __('Recent Households') }}</h3>
                        <a href="{{ route('admin.households.index') }}" class="text-sm text-teal-600 hover:text-teal-800">{{ __('messages.actions.view_all') }}</a>
                    </div>
                    <div class="divide-y">
                        @forelse($recentHouseholds as $household)
                            <a href="{{ route('admin.households.show', $household) }}" class="block p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $household->head_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $household->head_national_id }} • {{ $household->region->name ?? __('messages.general.unknown_region') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($household->status === 'verified') bg-green-100 text-green-800
                                        @elseif($household->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ __('messages.status.' . $household->status) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-gray-500">{{ __('No households yet') }}</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Distributions -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">{{ __('Recent Distributions') }}</h3>
                        <a href="{{ route('admin.distributions.index') }}" class="text-sm text-teal-600 hover:text-teal-800">{{ __('messages.actions.view_all') }}</a>
                    </div>
                    <div class="divide-y">
                        @forelse($recentDistributions as $dist)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $dist->household->head_name ?? __('messages.general.unknown') }}</p>
                                        <p class="text-sm text-gray-500">{{ $dist->aidProgram->name ?? __('messages.general.unknown') }}</p>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $dist->distribution_date->format('M j') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">{{ __('No distributions yet') }}</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">{{ __('Recent Users') }}</h3>
                        <a href="{{ route('admin.households.index') }}" class="text-sm text-teal-600 hover:text-teal-800">{{ __('messages.actions.view_all') }}</a>
                    </div>
                    <div class="divide-y">
                        @forelse($recentUsers as $user)
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->full_name ?? $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->national_id }} @if($user->phone) • {{ $user->phone }} @endif</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $user->hasRole('admin') ? 'bg-indigo-100 text-indigo-700' : ($user->hasRole('citizen') ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $user->getRoleNames()->first() ?? 'User' }}
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">{{ __('No users yet') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
