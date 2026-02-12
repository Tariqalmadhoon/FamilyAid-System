<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('citizen.dashboard') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.citizen.update_household') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('citizen.household.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Region Select -->
                    <div class="mb-4">
                        <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.household.region') }} <span class="text-red-500">*</span></label>
                        <select 
                            id="region_id" 
                            name="region_id" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            required
                        >
                            <option value="">{{ __('messages.onboarding_form.select_region_placeholder') }}</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $household->region_id) == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
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
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.full_address_placeholder') }}"
                            required
                        >{{ old('address_text', $household->address_text) }}</textarea>
                        @error('address_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Account -->
                    <div class="mb-4">
                        <label for="payment_account_type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.payment_account_type') }} <span class="text-red-500">*</span></label>
                        <select
                            id="payment_account_type"
                            name="payment_account_type"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            required
                        >
                            <option value="">{{ __('messages.actions.select') }}</option>
                            <option value="wallet" {{ old('payment_account_type', $household->payment_account_type) === 'wallet' ? 'selected' : '' }}>{{ __('messages.account_types.wallet') }}</option>
                            <option value="bank" {{ old('payment_account_type', $household->payment_account_type) === 'bank' ? 'selected' : '' }}>{{ __('messages.account_types.bank') }}</option>
                        </select>
                        @error('payment_account_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_account_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.payment_account_number') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="payment_account_number"
                            name="payment_account_number"
                            value="{{ old('payment_account_number', $household->payment_account_number) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.payment_account_number_placeholder') }}"
                            maxlength="30"
                            inputmode="numeric"
                            oninput="this.value=this.value.replace(/\\D/g,'').slice(0,30)"
                            required
                        >
                        @error('payment_account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_account_holder_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.payment_account_holder_name') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="payment_account_holder_name"
                            name="payment_account_holder_name"
                            value="{{ old('payment_account_holder_name', $household->payment_account_holder_name) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.payment_account_holder_name_placeholder') }}"
                            maxlength="255"
                            required
                        >
                        @error('payment_account_holder_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Housing Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.onboarding_form.housing_type') }} <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($housingTypes as $value => $label)
                                <label class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition {{ old('housing_type', $household->housing_type) === $value ? 'border-teal-500 bg-teal-50' : 'border-gray-200' }}">
                                    <input type="radio" name="housing_type" value="{{ $value }}" class="sr-only" {{ old('housing_type', $household->housing_type) === $value ? 'checked' : '' }}>
                                    <span class="text-sm font-medium {{ old('housing_type', $household->housing_type) === $value ? 'text-teal-700' : 'text-gray-700' }}">{{ $label }}</span>
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
                            value="{{ old('primary_phone', $household->primary_phone) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.primary_phone_placeholder') }}"
                            required
                            maxlength="10"
                            inputmode="numeric"
                            oninput="this.value=this.value.replace(/\\D/g,'').slice(0,10)"
                        >
                        @error('primary_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Phone -->
                    <div class="mb-6">
                        <label for="secondary_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.onboarding_form.secondary_phone') }}</label>
                        <input 
                            type="tel" 
                            id="secondary_phone" 
                            name="secondary_phone" 
                            value="{{ old('secondary_phone', $household->secondary_phone) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.secondary_phone_placeholder') }}"
                            maxlength="10"
                            inputmode="numeric"
                            oninput="this.value=this.value.replace(/\\D/g,'').slice(0,10)"
                        >
                    </div>

                    <!-- Health Conditions -->
                    <div class="mb-6 border-t pt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.health.section_title') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="has_war_injury" value="1" {{ old('has_war_injury', $household->has_war_injury) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span class="text-sm text-gray-700">{{ __('messages.health.has_war_injury') }}</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="has_chronic_disease" value="1" {{ old('has_chronic_disease', $household->has_chronic_disease) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span class="text-sm text-gray-700">{{ __('messages.health.has_chronic_disease') }}</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="has_disability" value="1" {{ old('has_disability', $household->has_disability) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                <span class="text-sm text-gray-700">{{ __('messages.health.has_disability') }}</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_type') }}</label>
                            <input type="text" name="condition_type" value="{{ old('condition_type', $household->condition_type) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.health.condition_type_placeholder') }}">
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_notes') }}</label>
                            <textarea name="condition_notes" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('messages.health.condition_notes_placeholder') }}">{{ old('condition_notes', $household->condition_notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t">
                        <a href="{{ route('citizen.dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                            {{ __('messages.actions.cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition">
                            {{ __('messages.actions.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Radio button visual toggle
        document.querySelectorAll('input[name="housing_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="housing_type"]').forEach(r => {
                    const label = r.closest('label');
                    if (r.checked) {
                        label.classList.add('border-teal-500', 'bg-teal-50');
                        label.classList.remove('border-gray-200');
                        label.querySelector('span').classList.add('text-teal-700');
                        label.querySelector('span').classList.remove('text-gray-700');
                    } else {
                        label.classList.remove('border-teal-500', 'bg-teal-50');
                        label.classList.add('border-gray-200');
                        label.querySelector('span').classList.remove('text-teal-700');
                        label.querySelector('span').classList.add('text-gray-700');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
