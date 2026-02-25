<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.households.show', $household) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('messages.actions.edit') }}: {{ $household->head_name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm">
                <form id="household-edit-form" method="POST" action="{{ route('admin.households.update', $household) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.head_national_id') }} <span class="text-red-500">*</span></label>
                            <input type="tel" name="head_national_id" value="{{ old('head_national_id', $household->head_national_id) }}" maxlength="9" inputmode="numeric" oninput="this.value=this.value.replace(/\\D/g,'').slice(0,9)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            @error('head_national_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.head_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="head_name" value="{{ old('head_name', $household->head_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.onboarding_form.spouse_section_title') }} <span class="text-red-500">*</span></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.spouse_full_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="spouse_full_name" value="{{ old('spouse_full_name', $household->spouse_full_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                @error('spouse_full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.spouse_national_id') }} <span class="text-red-500">*</span></label>
                                <input type="tel" name="spouse_national_id" value="{{ old('spouse_national_id', $household->spouse_national_id) }}" maxlength="9" inputmode="numeric" oninput="this.value=this.value.replace(/\\D/g,'').slice(0,9)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                @error('spouse_national_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.spouse_birth_date') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="spouse_birth_date" value="{{ old('spouse_birth_date', optional($household->spouse_birth_date)->toDateString()) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                @error('spouse_birth_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('messages.onboarding_form.spouse_health_title') }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="spouse_has_war_injury" value="1" {{ old('spouse_has_war_injury', $household->spouse_has_war_injury) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.war_injury') }}</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="spouse_has_chronic_disease" value="1" {{ old('spouse_has_chronic_disease', $household->spouse_has_chronic_disease) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.chronic_disease') }}</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="spouse_has_disability" value="1" {{ old('spouse_has_disability', $household->spouse_has_disability) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.disability') }}</span>
                                </label>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ __('messages.onboarding_form.spouse_condition_type_required_hint') }}</p>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.spouse_condition_type') }}</label>
                                <input type="text" name="spouse_condition_type" value="{{ old('spouse_condition_type', $household->spouse_condition_type) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.onboarding_form.spouse_condition_type_placeholder') }}">
                                @error('spouse_condition_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.spouse_health_notes') }}</label>
                                <textarea name="spouse_health_notes" rows="2" placeholder="{{ __('messages.onboarding_form.spouse_health_notes_placeholder') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">{{ old('spouse_health_notes', $household->spouse_health_notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.region') }} <span class="text-red-500">*</span></label>
                        <select name="region_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            @foreach($regions as $region)
                                <optgroup label="{{ $region->name }}">
                                    @foreach($region->children as $child)
                                        <option value="{{ $child->id }}" {{ old('region_id', $household->region_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.address') }}</label>
                        <textarea name="address_text" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('address_text', $household->address_text) }}</textarea>
                    </div>

                    @php
                        $selectedPreviousGovernorate = old('previous_governorate', $household->previous_governorate);
                        $selectedPreviousArea = old('previous_area', $household->previous_area);
                        $previousAreaOptions = $previousAreas[$selectedPreviousGovernorate] ?? [];
                    @endphp
                    <!-- Previous Residence -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.previous_governorate') }} <span class="text-red-500">*</span></label>
                            <select id="previous_governorate" name="previous_governorate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                <option value="">{{ __('messages.onboarding_form.previous_governorate_placeholder') }}</option>
                                @foreach($previousGovernorates as $key => $label)
                                    <option value="{{ $key }}" @selected($selectedPreviousGovernorate === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('previous_governorate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.previous_area') }} <span class="text-red-500">*</span></label>
                            <select id="previous_area" name="previous_area" data-selected="{{ $selectedPreviousArea }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 disabled:bg-gray-100 disabled:cursor-not-allowed" @disabled(!$selectedPreviousGovernorate) required>
                                <option value="">{{ __('messages.onboarding_form.previous_area_placeholder') }}</option>
                                @foreach($previousAreaOptions as $aKey => $aLabel)
                                    <option value="{{ $aKey }}" @selected($selectedPreviousArea === $aKey)>{{ $aLabel }}</option>
                                @endforeach
                            </select>
                            @error('previous_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.housing_type') }}</label>
                            <select name="housing_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <option value="">{{ __('messages.actions.select') }}</option>
                                @foreach($housingTypes as $type)
                                    <option value="{{ $type }}" {{ old('housing_type', $household->housing_type) === $type ? 'selected' : '' }}>{{ __('messages.housing_types.' . $type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.status.status') }}</label>
                            <div class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                {{ __('messages.status.' . $household->status) }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500">يتم تغيير الحالة من إجراء التوثيق فقط.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.primary_phone') }}</label>
                            <input type="tel" name="primary_phone" value="{{ old('primary_phone', $household->primary_phone) }}" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\\D/g,'').slice(0,10)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.secondary_phone') }}</label>
                            <input type="tel" name="secondary_phone" value="{{ old('secondary_phone', $household->secondary_phone) }}" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\\D/g,'').slice(0,10)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- Health Conditions Section -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.health.section_title') }}</h3>
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="has_war_injury" value="1" {{ old('has_war_injury', $household->has_war_injury) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span>{{ __('messages.health.war_injury') }}</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="has_chronic_disease" value="1" {{ old('has_chronic_disease', $household->has_chronic_disease) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span>{{ __('messages.health.chronic_disease') }}</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="has_disability" value="1" {{ old('has_disability', $household->has_disability) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span>{{ __('messages.health.disability') }}</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_type') }}</label>
                            <input type="text" name="condition_type" value="{{ old('condition_type', $household->condition_type) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.health.condition_type_placeholder') }}">
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_notes') }}</label>
                            <textarea name="condition_notes" rows="2" placeholder="{{ __('messages.health.condition_notes_placeholder') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">{{ old('condition_notes', $household->condition_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.program.notes') }}</label>
                        <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes', $household->notes) }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" onclick="if(confirm('{{ __('messages.confirm.delete') }}')) document.getElementById('delete-household-form').submit();" class="px-4 py-2 text-red-600 hover:text-red-800 text-sm">
                            {{ __('messages.actions.delete') }} {{ __('messages.households_admin.title') ?? '' }}
                        </button>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.households.show', $household) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('messages.actions.cancel') }}</a>
                            <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700">{{ __('messages.actions.save') }}</button>
                        </div>
                    </div>
                </form>
                <form id="delete-household-form" action="{{ route('admin.households.destroy', $household) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const governorateSelect = document.getElementById('previous_governorate');
            const areaSelect = document.getElementById('previous_area');
            if (!governorateSelect || !areaSelect) {
                return;
            }

            const allAreas = @json($previousAreas);
            const areaPlaceholder = @json(__('messages.onboarding_form.previous_area_placeholder'));

            const populateAreas = function (selectedValue) {
                const governorate = governorateSelect.value;
                const options = allAreas[governorate] || {};

                areaSelect.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = areaPlaceholder;
                areaSelect.appendChild(placeholderOption);

                Object.entries(options).forEach(function ([key, label]) {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = label;
                    areaSelect.appendChild(option);
                });

                areaSelect.disabled = !governorate;

                if (selectedValue && Object.prototype.hasOwnProperty.call(options, selectedValue)) {
                    areaSelect.value = selectedValue;
                }
            };

            governorateSelect.addEventListener('change', function () {
                populateAreas('');
            });

            populateAreas(areaSelect.dataset.selected || areaSelect.value || '');
        });
    </script>
</x-app-layout>

