<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.onboarding_form.title') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="onboardingWizard()">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8 px-4">
                <div class="flex items-center justify-between">
                    <template x-for="(stepInfo, index) in steps" :key="index">
                        <div class="flex items-center" :class="index < steps.length - 1 ? 'flex-1' : ''">
                            <div class="flex items-center">
                                <div 
                                    class="flex items-center justify-center w-10 h-10 rounded-full font-bold transition-all duration-300"
                                    :class="step > index ? 'bg-teal-600 text-white' : (step === index ? 'bg-teal-600 text-white ring-4 ring-teal-200' : 'bg-gray-200 text-gray-600')"
                                >
                                    <span x-show="step <= index" x-text="index + 1"></span>
                                    <svg x-show="step > index" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="ml-2 text-sm font-medium hidden sm:inline" :class="step >= index ? 'text-teal-600' : 'text-gray-400'" x-text="stepInfo.title"></span>
                            </div>
                            <div x-show="index < steps.length - 1" class="flex-1 h-1 mx-4 rounded transition-all duration-300" :class="step > index ? 'bg-teal-600' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('citizen.onboarding.store') }}" @submit="handleSubmit">
                    @csrf

                    <!-- Step 1: Region & Address -->
                    <div 
                        x-show="step === 0"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                        class="p-6"
                    >
                        <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('messages.onboarding_form.section_region_address') }}</h3>

                        <!-- Region Select -->
                        <div class="mb-4">
                            <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.select_region') }} <span class="text-red-500">*</span></label>
                            <select 
                                id="region_id" 
                                name="region_id" 
                                x-model="formData.region_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                required
                            >
                                <option value="">{{ __('messages.onboarding_form.select_region_placeholder') }}</option>
                                @foreach($regions as $region)
                                    <optgroup label="{{ $region->name }}">
                                        @foreach($region->children as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('region_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address_text" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.full_address') }} <span class="text-red-500">*</span></label>
                            <textarea 
                                id="address_text" 
                                name="address_text" 
                                x-model="formData.address_text"
                                rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                placeholder="{{ __('messages.onboarding_form.full_address_placeholder') }}"
                                required
                            ></textarea>
                            @error('address_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 2: Housing & Contact -->
                    <div 
                        x-show="step === 1"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                        class="p-6"
                    >
                        <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('messages.onboarding_form.housing_contact') }}</h3>

                        <!-- Housing Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.onboarding_form.housing_type') }} <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($housingTypes as $value => $label)
                                    <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                           :class="formData.housing_type === '{{ $value }}' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'">
                                        <input type="radio" name="housing_type" value="{{ $value }}" x-model="formData.housing_type" class="sr-only">
                                        <span class="text-sm font-medium" :class="formData.housing_type === '{{ $value }}' ? 'text-teal-700' : 'text-gray-700'">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('housing_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Primary Phone -->
                        <div class="mb-4">
                            <label for="primary_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.primary_phone') }} <span class="text-red-500">*</span></label>
                            <input 
                                type="tel" 
                                id="primary_phone" 
                                name="primary_phone" 
                                x-model="formData.primary_phone"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                placeholder="{{ __('messages.onboarding_form.primary_phone_placeholder') }}"
                                required
                            >
                            @error('primary_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Secondary Phone -->
                        <div class="mb-4">
                            <label for="secondary_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.secondary_phone') }}</label>
                            <input 
                                type="tel" 
                                id="secondary_phone" 
                                name="secondary_phone" 
                                x-model="formData.secondary_phone"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                placeholder="{{ __('messages.onboarding_form.secondary_phone_placeholder') }}"
                            >
                        </div>
                    </div>

                    <!-- Step 3: Family Members -->
                    <div 
                        x-show="step === 2"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                        class="p-6"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('messages.onboarding_form.family_members_title') }}</h3>
                            <button 
                                type="button" 
                                @click="addMember"
                                class="inline-flex items-center px-3 py-1.5 bg-teal-100 text-teal-700 rounded-lg hover:bg-teal-200 transition text-sm font-medium"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('messages.onboarding_form.add_member') }}
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 mb-4">{{ __('messages.onboarding_form.members_helper') }}</p>

                        <!-- Members List -->
                        <div class="space-y-4">
                            <template x-for="(member, index) in members" :key="index">
                                <div 
                                    class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                >
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">{{ __('messages.onboarding_form.member_label') }} <span x-text="index + 1"></span></span>
                                        <button type="button" @click="removeMember(index)" class="text-red-500 hover:text-red-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.onboarding_form.member_full_name') }} <span class="text-red-500">*</span></label>
                                            <input 
                                                type="text" 
                                                :name="'members[' + index + '][full_name]'"
                                                x-model="member.full_name"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                                                placeholder="{{ __('messages.onboarding_form.member_full_name') }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.onboarding_form.member_relation') }} <span class="text-red-500">*</span></label>
                                            <select 
                                                :name="'members[' + index + '][relation_to_head]'"
                                                x-model="member.relation_to_head"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                                                required
                                            >
                                                <option value="">{{ __('messages.actions.select') }}</option>
                                                @foreach($relations as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.onboarding_form.member_national_id_optional') }}</label>
                                            <input 
                                                type="text" 
                                                :name="'members[' + index + '][national_id]'"
                                                x-model="member.national_id"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                                                placeholder="{{ __('messages.onboarding_form.member_national_id_optional') }}"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.onboarding_form.member_gender') }}</label>
                                            <select 
                                                :name="'members[' + index + '][gender]'"
                                                x-model="member.gender"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                                            >
                                                <option value="">{{ __('messages.actions.select') }}</option>
                                                <option value="male">{{ __('messages.onboarding_form.member_gender_male') }}</option>
                                                <option value="female">{{ __('messages.onboarding_form.member_gender_female') }}</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.onboarding_form.member_birth_date_optional') }}</label>
                                            <input 
                                                type="date" 
                                                :name="'members[' + index + '][birth_date]'"
                                                x-model="member.birth_date"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="members.length === 0" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-sm">{{ __('messages.onboarding_form.members_empty_title') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('messages.onboarding_form.members_empty_helper') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Review -->
                    <div 
                        x-show="step === 3"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                        class="p-6"
                    >
                        <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('messages.onboarding_form.review_title') }}</h3>

                        <div class="space-y-6">
                            <!-- Address Summary -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-700">{{ __('messages.onboarding_form.address_info') }}</h4>
                                    <button type="button" @click="step = 0" class="text-teal-600 hover:text-teal-800 text-sm">{{ __('messages.onboarding_form.edit') }}</button>
                                </div>
                                <p class="text-sm text-gray-600" x-text="formData.address_text || '{{ __('messages.onboarding_form.not_provided') }}'"></p>
                            </div>

                            <!-- Housing Summary -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-700">{{ __('messages.onboarding_form.housing_info') }}</h4>
                                    <button type="button" @click="step = 1" class="text-teal-600 hover:text-teal-800 text-sm">{{ __('messages.onboarding_form.edit') }}</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">{{ __('messages.onboarding_form.housing_label') }}</span>
                                        <span class="text-gray-700 ml-1 capitalize" x-text="formData.housing_type?.replace('_', ' ') || '{{ __('messages.onboarding_form.not_selected') }}'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">{{ __('messages.onboarding_form.phone_label') }}</span>
                                        <span class="text-gray-700 ml-1" x-text="formData.primary_phone || '{{ __('messages.onboarding_form.not_provided') }}'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Members Summary -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-700">{{ __('messages.onboarding_form.members_summary_title') }} (<span x-text="members.length"></span>)</h4>
                                    <button type="button" @click="step = 2" class="text-teal-600 hover:text-teal-800 text-sm">{{ __('messages.onboarding_form.edit') }}</button>
                                </div>
                                <div x-show="members.length > 0" class="space-y-2">
                                    <template x-for="(member, index) in members" :key="index">
                                        <div class="flex items-center text-sm">
                                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs mr-2" x-text="index + 1"></span>
                                            <span class="text-gray-700" x-text="member.full_name"></span>
                                            <span class="text-gray-400 mx-1">-</span>
                                            <span class="text-gray-500 capitalize" x-text="member.relation_to_head"></span>
                                        </div>
                                    </template>
                                </div>
                                <p x-show="members.length === 0" class="text-sm text-gray-500">{{ __('messages.onboarding_form.members_none') }}</p>
                            </div>

                            <!-- Status Notice -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">{{ __('messages.onboarding_form.pending_verification_title') }}</p>
                                        <p class="text-sm text-yellow-700 mt-1">{{ __('messages.onboarding_form.pending_verification_text') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
                        <button 
                            type="button" 
                            @click="prevStep"
                            x-show="step > 0"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            {{ __('messages.onboarding_form.btn_previous') }}
                        </button>
                        <div x-show="step === 0"></div>

                        <button 
                            type="button" 
                            @click="nextStep"
                            x-show="step < 3"
                            :disabled="!canProceed"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-teal-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ __('messages.onboarding_form.btn_next') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <button 
                            type="submit" 
                            x-show="step === 3"
                            :disabled="submitting"
                            class="inline-flex items-center px-6 py-2 bg-teal-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-teal-700 transition disabled:opacity-50"
                        >
                            <span x-show="!submitting">{{ __('messages.onboarding_form.btn_submit') }}</span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('messages.onboarding_form.submitting') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function onboardingWizard() {
            return {
                step: 0,
                submitting: false,
                steps: [
                    { title: '{{ __('messages.onboarding_form.step_address') }}' },
                    { title: '{{ __('messages.onboarding_form.step_housing') }}' },
                    { title: '{{ __('messages.onboarding_form.step_members') }}' },
                    { title: '{{ __('messages.onboarding_form.step_review') }}' }
                ],
                formData: {
                    region_id: '',
                    address_text: '',
                    housing_type: '',
                    primary_phone: '',
                    secondary_phone: ''
                },
                members: [],

                get canProceed() {
                    if (this.step === 0) {
                        return this.formData.region_id && this.formData.address_text;
                    }
                    if (this.step === 1) {
                        return this.formData.housing_type && this.formData.primary_phone;
                    }
                    return true;
                },

                nextStep() {
                    if (this.step < 3 && this.canProceed) {
                        this.step++;
                    }
                },

                prevStep() {
                    if (this.step > 0) {
                        this.step--;
                    }
                },

                addMember() {
                    this.members.push({
                        full_name: '',
                        national_id: '',
                        relation_to_head: '',
                        gender: '',
                        birth_date: ''
                    });
                },

                removeMember(index) {
                    this.members.splice(index, 1);
                },

                handleSubmit(e) {
                    this.submitting = true;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
