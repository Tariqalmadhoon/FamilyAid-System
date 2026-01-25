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

    <div class="py-8" x-data="{ showToast: {{ session('success') ? 'true' : 'false' }}, toastMessage: '{{ session('success') }}' }">
        <!-- Toast Notification -->
        <div 
            x-show="showToast" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-init="showToast && setTimeout(() => showToast = false, 5000)"
            class="fixed bottom-4 right-4 z-50"
        >
            <div class="bg-teal-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span x-text="toastMessage"></span>
            </div>
        </div>

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
                                <optgroup label="{{ $region->name }}">
                                    @foreach($region->children as $child)
                                        <option value="{{ $child->id }}" {{ $household->region_id == $child->id ? 'selected' : '' }}>
                                            {{ $child->name }}
                                        </option>
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
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="{{ __('messages.onboarding_form.full_address_placeholder') }}"
                            required
                        >{{ old('address_text', $household->address_text) }}</textarea>
                        @error('address_text')
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
                        >
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
