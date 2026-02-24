<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.households.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $household->head_name }}</h2>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($household->status === 'verified') bg-green-100 text-green-800
                @elseif($household->status === 'pending') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ __('messages.status.' . $household->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">{{ __('messages.households_admin.details') ?? 'تفاصيل الأسرة' }}</h3>
                            <a href="{{ route('admin.households.edit', $household) }}" class="text-teal-600 hover:text-teal-800 text-sm">{{ __('messages.actions.edit') ?? 'تعديل' }}</a>
                        </div>
                        <dl class="grid grid-cols-2 gap-4">
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.head_national_id') ?? 'الرقم الوطني لرب الأسرة' }}</dt><dd class="font-medium">{{ $household->head_national_id }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.onboarding_form.spouse_national_id') }}</dt><dd class="font-medium">{{ $household->spouse_national_id ?? '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.onboarding_form.spouse_full_name') }}</dt><dd class="font-medium">{{ $household->spouse_full_name ?? '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.onboarding_form.spouse_birth_date') }}</dt><dd class="font-medium">{{ optional($household->spouse_birth_date)->format('Y-m-d') ?? '-' }}</dd></div>
                            <div class="col-span-2">
                                <dt class="text-xs text-gray-500 uppercase">{{ __('messages.onboarding_form.spouse_health_title') }}</dt>
                                <dd class="mt-2">
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="px-2 py-1 rounded {{ $household->spouse_has_war_injury ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.war_injury') }}: {{ $household->spouse_has_war_injury ? 'نعم' : 'لا' }}</span>
                                        <span class="px-2 py-1 rounded {{ $household->spouse_has_chronic_disease ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.chronic_disease') }}: {{ $household->spouse_has_chronic_disease ? 'نعم' : 'لا' }}</span>
                                        <span class="px-2 py-1 rounded {{ $household->spouse_has_disability ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.disability') }}: {{ $household->spouse_has_disability ? 'نعم' : 'لا' }}</span>
                                    </div>
                                    <p class="text-sm font-medium mt-2 text-slate-700">{{ $household->spouse_condition_type ?? __('messages.onboarding_form.not_provided') }}</p>
                                    @if($household->spouse_health_notes)
                                        <p class="text-xs text-gray-600 mt-1">{{ $household->spouse_health_notes }}</p>
                                    @endif
                                </dd>
                            </div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.region') ?? 'المنطقة' }}</dt><dd class="font-medium">{{ $household->region->name ?? '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.housing_type') ?? 'نوع السكن' }}</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $household->housing_type ?? '-') }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.primary_phone') ?? 'الهاتف الأساسي' }}</dt><dd class="font-medium">{{ $household->primary_phone ?? '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_type') }}</dt><dd class="font-medium">{{ $household->payment_account_type ? __('messages.account_types.' . $household->payment_account_type) : '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_number') }}</dt><dd class="font-medium">{{ $household->payment_account_number ?? '-' }}</dd></div>
                            <div class="col-span-2"><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.payment_account_holder_name') }}</dt><dd class="font-medium">{{ $household->payment_account_holder_name ?? '-' }}</dd></div>
                            <div class="col-span-2"><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.address') ?? 'العنوان' }}</dt><dd class="font-medium">{{ $household->address_text ?? '-' }}</dd></div>
                            @if($household->previous_governorate)
                                <div class="col-span-2">
                                    <dt class="text-xs text-gray-500 uppercase">{{ __('messages.onboarding_form.previous_residence_title') }}</dt>
                                    <dd class="font-medium">
                                        {{ __('messages.previous_governorates.' . $household->previous_governorate) }}
                                        @if($household->previous_area)
                                            — {{ __('messages.previous_areas.' . $household->previous_governorate . '.' . $household->previous_area) }}
                                        @endif
                                    </dd>
                                </div>
                            @endif
                            <div class="col-span-2">
                                <dt class="text-xs text-gray-500 uppercase">{{ __('messages.health.section_title') ?? 'الحالة الصحية' }}</dt>
                                <dd class="mt-2">
                                    <div class="border rounded-lg divide-y text-sm">
                                        <div class="flex items-center justify-between px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $household->has_war_injury ? 'bg-red-500' : 'bg-gray-300' }}"></span>
                                                <span class="text-gray-700">{{ __('messages.health.has_war_injury') }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded {{ $household->has_war_injury ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $household->has_war_injury ? 'نعم' : 'لا' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $household->has_chronic_disease ? 'bg-amber-500' : 'bg-gray-300' }}"></span>
                                                <span class="text-gray-700">{{ __('messages.health.has_chronic_disease') }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded {{ $household->has_chronic_disease ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $household->has_chronic_disease ? 'نعم' : 'لا' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $household->has_disability ? 'bg-indigo-500' : 'bg-gray-300' }}"></span>
                                                <span class="text-gray-700">{{ __('messages.health.has_disability') }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded {{ $household->has_disability ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $household->has_disability ? 'نعم' : 'لا' }}
                                            </span>
                                        </div>
                                        <div class="px-3 py-3 bg-slate-50">
                                            <p class="text-xs text-gray-500 mb-1">{{ __('messages.health.condition_type') }}</p>
                                            <p class="text-sm font-medium text-slate-700">{{ $household->condition_type ?? __('messages.onboarding_form.not_provided') }}</p>
                                            @if($household->condition_notes)
                                                <p class="text-xs text-gray-600 mt-2 leading-relaxed">{{ $household->condition_notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Members -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-4">{{ __('messages.household.members') ?? 'أفراد الأسرة' }} ({{ $household->members->count() }})</h3>
                        @if($household->members->count() > 0)
                            <div class="divide-y">
                                @foreach($household->members as $member)
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $member->full_name }}</p>
                                            <p class="text-sm text-gray-500 capitalize">{{ $member->relation_to_head }} @if($member->national_id) • {{ $member->national_id }} @endif</p>
                                            <div class="mt-2 text-xs text-gray-700 border rounded-lg divide-y">
                                                <div class="flex items-center justify-between px-2 py-1.5">
                                                    <span>{{ __('messages.health.has_war_injury') }}</span>
                                                    <span class="px-2 py-0.5 rounded font-medium {{ $member->has_war_injury ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $member->has_war_injury ? 'نعم' : 'لا' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between px-2 py-1.5">
                                                    <span>{{ __('messages.health.has_chronic_disease') }}</span>
                                                    <span class="px-2 py-0.5 rounded font-medium {{ $member->has_chronic_disease ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $member->has_chronic_disease ? 'نعم' : 'لا' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between px-2 py-1.5">
                                                    <span>{{ __('messages.health.has_disability') }}</span>
                                                    <span class="px-2 py-0.5 rounded font-medium {{ $member->has_disability ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $member->has_disability ? 'نعم' : 'لا' }}
                                                    </span>
                                                </div>
                                                <div class="px-2 py-2 bg-slate-50">
                                                    <p class="text-[11px] text-gray-500 mb-1">{{ __('messages.health.condition_type') }}</p>
                                                    <p class="text-sm font-medium text-slate-700">{{ $member->condition_type ?? __('messages.onboarding_form.not_provided') }}</p>
                                                </div>
                                            </div>
                                            @if($member->health_notes)
                                                <p class="text-xs text-gray-500 mt-1">{{ $member->health_notes }}</p>
                                            @endif
                                        </div>
                                        @if($member->birth_date)
                                            <span class="text-sm text-gray-500">{{ $member->age }} yrs</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">{{ __('messages.household.no_members') ?? 'لا يوجد أفراد مضافة' }}</p>
                        @endif
                    </div>

                    <!-- Distributions -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">{{ __('messages.distributions.history') ?? 'سجل التوزيعات' }}</h3>
                            <a href="{{ route('admin.distributions.create', ['household_id' => $household->id]) }}" class="text-teal-600 hover:text-teal-800 text-sm">+ {{ __('messages.distributions.record') ?? 'تسجيل' }}</a>
                        </div>
                        @if($household->distributions->count() > 0)
                            <div class="divide-y">
                                @foreach($household->distributions as $dist)
                                    <div class="py-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $dist->aidProgram->name ?? __('messages.general.unknown') }}</p>
                                            <p class="text-sm text-gray-500">{{ $dist->distribution_date->format('Y-m-d') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">{{ __('messages.distributions.none') ?? 'لا يوجد توزيعات بعد' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-4">{{ __('messages.actions.title') ?? 'الإجراءات' }}</h3>
                        <div class="space-y-2">
                            @if($household->status === 'pending')
                                <form action="{{ route('admin.households.verify', $household) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-2 px-4 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">{{ __('messages.households_admin.verify') ?? 'توثيق الأسرة' }}</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.distributions.create', ['household_id' => $household->id]) }}" class="block text-center py-2 px-4 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700">{{ __('messages.households_admin.record_distribution') ?? 'تسجيل توزيع' }}</a>
                            <a href="{{ route('admin.households.edit', $household) }}" class="block text-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('messages.actions.edit') ?? 'تعديل' }}</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-4">{{ __('messages.timeline.title') ?? 'السجل الزمني' }}</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center text-gray-500">
                                <span class="w-2 h-2 bg-teal-500 rounded-full mr-2"></span>
                                {{ __('messages.timeline.created') ?? 'تم الإنشاء في' }} {{ $household->created_at->format('Y-m-d') }}
                            </div>
                            <div class="flex items-center text-gray-500">
                                <span class="w-2 h-2 bg-gray-300 rounded-full mr-2"></span>
                                {{ __('messages.timeline.updated') ?? 'آخر تحديث' }} {{ $household->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
