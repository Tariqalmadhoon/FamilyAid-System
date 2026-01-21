<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.distributions.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record Distribution') }}</h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="distributionForm()">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form method="POST" action="{{ route('admin.distributions.store') }}">
                    @csrf

                    <!-- Household Search -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Household <span class="text-red-500">*</span></label>
                        
                        @if($household)
                            <div class="p-3 bg-teal-50 border border-teal-200 rounded-lg mb-2">
                                <p class="font-medium text-teal-800">{{ $household->head_name }}</p>
                                <p class="text-sm text-teal-600">{{ $household->head_national_id }} • {{ $household->region->name ?? '' }}</p>
                            </div>
                            <input type="hidden" name="household_id" value="{{ $household->id }}">
                        @else
                            <input type="hidden" name="household_id" x-model="selectedHousehold.id">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="searchQuery" 
                                    @input.debounce.300ms="search"
                                    @focus="showResults = true"
                                    placeholder="Search by National ID, name, or phone..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                >
                                <div x-show="showResults && results.length > 0" @click.away="showResults = false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                    <template x-for="result in results" :key="result.id">
                                        <div @click="selectHousehold(result)" class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                                            <p class="font-medium text-gray-900" x-text="result.head_name"></p>
                                            <p class="text-sm text-gray-500"><span x-text="result.head_national_id"></span> • <span x-text="result.region"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div x-show="selectedHousehold.id" class="mt-2 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-teal-800" x-text="selectedHousehold.head_name"></p>
                                        <p class="text-sm text-teal-600"><span x-text="selectedHousehold.head_national_id"></span></p>
                                    </div>
                                    <button type="button" @click="clearSelection" class="text-teal-600 hover:text-teal-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @error('household_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Program Select -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aid Program <span class="text-red-500">*</span></label>
                        <select name="aid_program_id" x-model="selectedProgram" @change="checkEligibility" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <option value="">-- Select Program --</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                        @error('aid_program_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        
                        <!-- Eligibility Message -->
                        <div x-show="eligibilityMessage" class="mt-2 p-2 rounded text-sm" :class="isEligible ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
                            <span x-text="eligibilityMessage"></span>
                        </div>
                    </div>

                    <!-- Distribution Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="distribution_date" value="{{ old('distribution_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.distributions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" :disabled="!isEligible && selectedProgram && (selectedHousehold.id || {{ $household ? 'true' : 'false' }})" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed">Record Distribution</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function distributionForm() {
            return {
                searchQuery: '',
                results: [],
                showResults: false,
                selectedHousehold: { id: null, head_name: '', head_national_id: '' },
                selectedProgram: '',
                eligibilityMessage: '',
                isEligible: true,

                async search() {
                    if (this.searchQuery.length < 2) {
                        this.results = [];
                        return;
                    }
                    const response = await fetch(`/admin/distributions/search-household?q=${encodeURIComponent(this.searchQuery)}`);
                    this.results = await response.json();
                },

                selectHousehold(household) {
                    this.selectedHousehold = household;
                    this.searchQuery = '';
                    this.showResults = false;
                    this.results = [];
                    this.checkEligibility();
                },

                clearSelection() {
                    this.selectedHousehold = { id: null, head_name: '', head_national_id: '' };
                    this.eligibilityMessage = '';
                },

                async checkEligibility() {
                    const householdId = this.selectedHousehold.id || {{ $household?->id ?? 'null' }};
                    if (!householdId || !this.selectedProgram) {
                        this.eligibilityMessage = '';
                        return;
                    }
                    const response = await fetch(`/admin/distributions/check-eligibility?household_id=${householdId}&program_id=${this.selectedProgram}`);
                    const data = await response.json();
                    this.eligibilityMessage = data.message;
                    this.isEligible = data.eligible;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
