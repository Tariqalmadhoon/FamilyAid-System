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
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.region') ?? 'المنطقة' }}</dt><dd class="font-medium">{{ $household->region->name ?? '-' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.housing_type') ?? 'نوع السكن' }}</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $household->housing_type ?? '-') }}</dd></div>
                            <div><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.primary_phone') ?? 'الهاتف الأساسي' }}</dt><dd class="font-medium">{{ $household->primary_phone ?? '-' }}</dd></div>
                            <div class="col-span-2"><dt class="text-xs text-gray-500 uppercase">{{ __('messages.household.address') ?? 'العنوان' }}</dt><dd class="font-medium">{{ $household->address_text ?? '-' }}</dd></div>
                            <div class="col-span-2">
                                <dt class="text-xs text-gray-500 uppercase">{{ __('messages.health.section_title') ?? 'الحالة الصحية' }}</dt>
                                <dd class="font-medium">
                                    <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                        <span class="px-2 py-1 rounded-full {{ $household->has_war_injury ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.has_war_injury') }}</span>
                                        <span class="px-2 py-1 rounded-full {{ $household->has_chronic_disease ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.has_chronic_disease') }}</span>
                                        <span class="px-2 py-1 rounded-full {{ $household->has_disability ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.has_disability') }}</span>
                                        @if($household->condition_type)
                                            <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $household->condition_type }}</span>
                                        @endif
                                    </div>
                                    @if($household->condition_notes)
                                        <p class="text-xs text-gray-500 mt-1">{{ $household->condition_notes }}</p>
                                    @endif
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
                                            <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                                @if($member->has_war_injury)
                                                    <span class="px-2 py-1 rounded-full bg-red-50 text-red-700">{{ __('messages.health.war_injury') }}</span>
                                                @endif
                                                @if($member->has_chronic_disease)
                                                    <span class="px-2 py-1 rounded-full bg-amber-50 text-amber-700">{{ __('messages.health.chronic_disease') }}</span>
                                                @endif
                                                @if($member->has_disability)
                                                    <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">{{ __('messages.health.disability') }}</span>
                                                @endif
                                                @if($member->condition_type)
                                                    <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $member->condition_type }}</span>
                                                @endif
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
