<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.citizen.dashboard_title') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($household->status === 'verified') bg-green-100 text-green-800
                @elseif($household->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($household->status === 'suspended') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ __('messages.status.' . $household->status) }}
            </span>
        </div>
    </x-slot>

    <style>
        .p-3{
            margin: 10px;
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Last Benefit Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('messages.citizen.last_benefit_title') }}
                    </h3>

                    @if($lastDistribution)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-lg border border-teal-100">
                            <div>
                                <p class="text-lg font-semibold text-teal-800">{{ $lastDistribution->aidProgram->name }}</p>
                                <p class="text-sm text-teal-600">{{ $lastDistribution->distribution_date->format('F j, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">{{ $lastDistribution->distribution_date->diffForHumans() }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p>{{ __('messages.citizen.no_benefits') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('messages.citizen.no_benefits_helper') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <a href="{{ route('citizen.household.edit') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 hover:border-teal-200 hover:shadow-md transition group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-teal-100 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ __('messages.citizen.update_household') }}</p>
                            <p class="text-xs text-gray-500">{{ __('messages.citizen.update_household_sub') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('citizen.members.index') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 hover:border-teal-200 hover:shadow-md transition group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ __('messages.citizen.manage_members') }}</p>
                            <p class="text-xs text-gray-500">{{ __('messages.citizen.member_count', ['count' => $household->members->count()]) }}</p>
                        </div>
                    </div>
                </a>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $household->region->name ?? __('messages.general.unknown_region') }}</p>
                            <p class="text-xs text-gray-500">{{ __('messages.citizen.your_region') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefit History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ __('messages.citizen.benefit_history') }}
                    </h3>

                    @if($distributions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.tracking.program') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.tracking.date') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.tracking.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($distributions as $distribution)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="font-medium text-gray-900">{{ $distribution->aidProgram->name }}</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $distribution->distribution_date->format('M j, Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $distribution->notes ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <p>{{ __('messages.citizen.benefit_history_empty') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Household Information Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('messages.citizen.household_info') }}
                        </h3>
                        <a href="{{ route('citizen.household.edit') }}" class="text-teal-600 hover:text-teal-800 text-sm font-medium">{{ __('messages.actions.edit') }}</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.head_name') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->head_name }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.head_national_id') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->head_national_id }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.phone') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->primary_phone ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.housing_type') }}</span>
                                <p class="text-sm font-medium text-gray-900 capitalize">
                                    {{ $household->housing_type ? __('messages.housing_types.' . $household->housing_type) : '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.address') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->address_text ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.registered_at') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->created_at->format('M j, Y') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_type') }}</span>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $household->payment_account_type ? __('messages.account_types.' . $household->payment_account_type) : '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_number') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->payment_account_number ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_holder_name') }}</span>
                                <p class="text-sm font-medium text-gray-900">{{ $household->payment_account_holder_name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
