<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.households_admin.title') }}
            </h2>
            <a href="{{ route('admin.households.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('messages.households_admin.add') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                @if($hasActiveFilters)
                    <div class="mb-3 p-3 rounded-md bg-yellow-50 text-yellow-800 text-sm flex items-center justify-between">
                        <span>{{ __('messages.households_admin.filters_active_notice') }}</span>
                        <a href="{{ route('admin.households.index') }}" class="text-yellow-900 underline hover:text-yellow-700">{{ __('messages.actions.clear') }}</a>
                    </div>
                @endif
                <form method="GET" class="space-y-4">
                    <!-- Row 1: Search, Status, Region, Housing -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('messages.households_admin.search_placeholder') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                        </div>
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                <option value="">{{ __('messages.households_admin.all_status') }}</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>{{ __('messages.status.pending') }}</option>
                                <option value="verified" {{ ($filters['status'] ?? '') === 'verified' ? 'selected' : '' }}>{{ __('messages.status.verified') }}</option>
                                <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>{{ __('messages.status.suspended') }}</option>
                                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>{{ __('messages.status.rejected') }}</option>
                            </select>
                        </div>
                        <div>
                            <select name="region_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                <option value="">{{ __('messages.households_admin.all_regions') }}</option>
                                @foreach($regions as $region)
                                    <optgroup label="{{ $region->name }}">
                                        @foreach($region->children as $child)
                                            <option value="{{ $child->id }}" {{ ($filters['region_id'] ?? '') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="housing_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                <option value="">{{ __('messages.households_admin.all_housing') }}</option>
                                <option value="owned" {{ ($filters['housing_type'] ?? '') === 'owned' ? 'selected' : '' }}>{{ __('messages.housing_types.owned') }}</option>
                                <option value="rented" {{ ($filters['housing_type'] ?? '') === 'rented' ? 'selected' : '' }}>{{ __('messages.housing_types.rented') }}</option>
                                <option value="family_hosted" {{ ($filters['housing_type'] ?? '') === 'family_hosted' ? 'selected' : '' }}>{{ __('messages.housing_types.family_hosted') }}</option>
                                <option value="other" {{ ($filters['housing_type'] ?? '') === 'other' ? 'selected' : '' }}>{{ __('messages.housing_types.other') }}</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-teal-700 transition">{{ __('messages.actions.filter') }}</button>
                            <a href="{{ route('admin.households.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">{{ __('messages.actions.clear') }}</a>
                        </div>
                    </div>
                    
                    <!-- Row 2: Health & Child Filters -->
                    <div class="flex flex-wrap gap-4 pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="has_war_injury" value="1" {{ ($filters['has_war_injury'] ?? '') ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span>{{ __('messages.health.has_war_injury') }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="has_chronic_disease" value="1" {{ ($filters['has_chronic_disease'] ?? '') ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span>{{ __('messages.health.has_chronic_disease') }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="has_disability" value="1" {{ ($filters['has_disability'] ?? '') ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span>{{ __('messages.health.has_disability') }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer {{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }}">
                            <input type="checkbox" name="has_child_under_2" value="1" {{ ($filters['has_child_under_2'] ?? '') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-blue-700 font-medium">{{ __('messages.child_filter.has_child_under_2') }}</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Results -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.head') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.national_id') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.region') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.members') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($households as $household)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.households.show', $household) }}" class="font-medium text-gray-900 hover:text-teal-600">{{ $household->head_name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $household->head_national_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $household->region->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $household->members->count() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($household->status === 'verified') bg-green-100 text-green-800
                                                @elseif($household->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($household->status === 'suspended') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ __('messages.status.' . $household->status) }}
                                            </span>
                                            @if($household->has_war_injury)
                                                <span class="px-1.5 py-0.5 text-xs rounded bg-orange-100 text-orange-700" title="{{ __('messages.health.war_injury') }}">🩹</span>
                                            @endif
                                            @if($household->has_chronic_disease)
                                                <span class="px-1.5 py-0.5 text-xs rounded bg-purple-100 text-purple-700" title="{{ __('messages.health.chronic_disease') }}">💊</span>
                                            @endif
                                            @if($household->has_disability)
                                                <span class="px-1.5 py-0.5 text-xs rounded bg-blue-100 text-blue-700" title="{{ __('messages.health.disability') }}">♿</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if($household->status === 'pending')
                                                <form action="{{ route('admin.households.verify', $household) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800" title="{{ __('messages.actions.verify') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.households.edit', $household) }}" class="text-gray-500 hover:text-teal-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.distributions.create', ['household_id' => $household->id]) }}" class="text-gray-500 hover:text-green-600" title="{{ __('messages.households_admin.record_distribution') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">{{ __('messages.households_admin.no_results') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($households->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $households->links() }}
                    </div>
                @endif
            </div>

            <!-- Pending users without households -->
            @if(! $hasActiveFilters && isset($pendingUsers) && $pendingUsers->count())
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-6">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">{{ __('messages.households_admin.pending_users_title') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('messages.households_admin.pending_users_hint') }}</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.head') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.national_id') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.households_admin.table.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingUsers as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->full_name ?? $user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->national_id }}</td>
                                        <td class="px-6 py-4 text-xs">
                                            <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">{{ __('messages.status.pending') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
