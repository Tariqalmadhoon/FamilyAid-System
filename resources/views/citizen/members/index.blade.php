<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('citizen.dashboard') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('messages.members.manage_title') }}
                </h2>
            </div>
            <span class="text-sm text-gray-500">{{ __('messages.members.count', ['count' => $members->count() + ($household->spouse_full_name ? 1 : 0)]) }}</span>
        </div>
    </x-slot>

    <div class="py-8" x-data="membersManager()">
        <!-- Toast Notification -->
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-init="@if(session('success')) showToast = true; toastMessage = '{{ session('success') }}'; setTimeout(() => showToast = false, 5000); @endif"
            class="fixed bottom-4 right-4 z-50"
        >
            <div class="px-6 py-3 rounded-lg shadow-lg flex items-center" :class="toastType === 'success' ? 'bg-teal-600 text-white' : 'bg-red-600 text-white'">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span x-text="toastMessage"></span>
            </div>
        </div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Member Button -->
            <div class="mb-4 flex justify-end">
                <button
                    @click="openAddModal"
                    class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('messages.members.add_btn') }}
                </button>
            </div>

            <div class="mb-4 bg-white border border-gray-100 rounded-lg shadow-sm p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ __('messages.onboarding_form.spouse_section_title') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $household->spouse_full_name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $household->spouse_national_id ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ optional($household->spouse_birth_date)->format('Y-m-d') ?? '-' }}</p>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded {{ $household->spouse_has_war_injury ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.war_injury') }}: {{ $household->spouse_has_war_injury ? 'نعم' : 'لا' }}</span>
                            <span class="px-2 py-1 rounded {{ $household->spouse_has_chronic_disease ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.chronic_disease') }}: {{ $household->spouse_has_chronic_disease ? 'نعم' : 'لا' }}</span>
                            <span class="px-2 py-1 rounded {{ $household->spouse_has_disability ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">{{ __('messages.health.disability') }}: {{ $household->spouse_has_disability ? 'نعم' : 'لا' }}</span>
                        </div>
                        @if($household->spouse_condition_type)
                            <p class="text-xs text-gray-600 mt-1">{{ $household->spouse_condition_type }}</p>
                        @endif
                        @if($household->spouse_health_notes)
                            <p class="text-xs text-gray-500 mt-1">{{ $household->spouse_health_notes }}</p>
                        @endif
                    </div>
                    <a href="{{ route('citizen.household.edit') }}" class="inline-flex items-center px-3 py-1.5 rounded-md border border-teal-200 bg-teal-50 text-teal-700 text-xs font-medium hover:bg-teal-100 transition">
                        {{ __('messages.actions.edit') }}
                    </a>
                </div>
            </div>

            <!-- Members List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($members->count() > 0)
                        <div class="space-y-4">
                            @foreach($members as $member)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <div style="margin: 10px" class="w-10 h-10 m-2.5 rounded-full flex items-center justify-center text-white font-bold {{ $member->gender === 'female' ? 'bg-pink-500' : 'bg-blue-500' }}">
                                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                                            <p class="text-sm text-gray-500 capitalize">{{ $member->relation_to_head ? __('messages.relations.' . $member->relation_to_head) : '-' }}</p>
                                            <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                                @if($member->has_war_injury)
                                                    <span class="px-2 py-1 rounded-full bg-red-50 text-red-600">{{ __('messages.health.war_injury') }}</span>
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
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right text-sm text-gray-500 hidden sm:block">
                                            @if($member->national_id)
                                                <p>{{ __('messages.members.id_label') }}: {{ $member->national_id }}</p>
                                            @endif
                                            @if($member->birth_date)
                                                <p>{{ __('messages.members.age_years', ['years' => $member->age]) }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button
                                                @click="openEditModal({{ $member->toJson() }})"
                                                class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button
                                                @click="confirmDelete({{ $member->id }}, '{{ $member->full_name }}')"
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('messages.members.none_title') }}</h3>
                            <p class="text-gray-500 mb-4">{{ __('messages.members.none_helper') }}</p>
                            <button
                                @click="openAddModal"
                                class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition"
                            >
                                {{ __('messages.members.add_first') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="closeModal"></div>

                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                >
                    <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="editingId ? '{{ __('messages.members.edit_title') }}' : '{{ __('messages.members.add_btn') }}'"></h3>

                    <form @submit.prevent="saveMember">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.members.full_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.full_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.members.relation') }} <span class="text-red-500">*</span></label>
                                <select x-model="form.relation_to_head" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                    @foreach($relations as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.members.national_id_optional') }} <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="form.national_id" maxlength="9" inputmode="numeric" @input="form.national_id = form.national_id.replace(/\\D/g,'').slice(0,9)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.members.gender') }}</label>
                                    <select x-model="form.gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        <option value="">{{ __('messages.actions.select') }}</option>
                                        <option value="male">{{ __('messages.members.male') }}</option>
                                        <option value="female">{{ __('messages.members.female') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.members.birth_date') }}</label>
                                    <input type="date" x-model="form.birth_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" x-model="form.has_war_injury" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.has_war_injury') }}</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" x-model="form.has_chronic_disease" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.has_chronic_disease') }}</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" x-model="form.has_disability" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <span>{{ __('messages.health.has_disability') }}</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_type') }}</label>
                                <input type="text" x-model="form.condition_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.health.condition_notes') }}</label>
                                <textarea x-model="form.health_notes" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                {{ __('messages.actions.cancel') }}
                            </button>
                            <button type="submit" :disabled="saving" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700 transition disabled:opacity-50">
                                <span x-show="!saving" x-text="editingId ? '{{ __('messages.actions.update') }}' : '{{ __('messages.members.add_btn') }}'"></span>
                                <span x-show="saving">{{ __('messages.members.saving') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            x-show="showDeleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>

                <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.members.remove_title') }}</h3>
                        <p class="text-sm text-gray-500 mb-4" x-text="`{{ __('messages.members.remove_confirm', ['name' => ':name']) }}`.replace(':name', deletingName)"></p>

                        <div class="flex justify-center space-x-3">
                            <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                {{ __('messages.actions.cancel') }}
                            </button>
                            <form :action="'/citizen/members/' + deletingId" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition">
                                    {{ __('messages.members.remove_title') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function membersManager() {
            return {
                showModal: false,
                showDeleteModal: false,
                showToast: false,
                toastMessage: '',
                toastType: 'success',
                saving: false,
                editingId: null,
                deletingId: null,
                deletingName: '',
                form: {
                    full_name: '',
                    national_id: '',
                    relation_to_head: 'son',
                    gender: '',
                    birth_date: '',
                    has_war_injury: false,
                    has_chronic_disease: false,
                    has_disability: false,
                    condition_type: '',
                    health_notes: ''
                },

                openAddModal() {
                    this.editingId = null;
                    this.form = { full_name: '', national_id: '', relation_to_head: 'son', gender: '', birth_date: '', has_war_injury: false, has_chronic_disease: false, has_disability: false, condition_type: '', health_notes: '' };
                    this.showModal = true;
                },

                openEditModal(member) {
                    this.editingId = member.id;
                    this.form = {
                        full_name: member.full_name,
                        national_id: member.national_id || '',
                        relation_to_head: 'son',
                        gender: member.gender || '',
                        birth_date: member.birth_date ? member.birth_date.split('T')[0] : '',
                        has_war_injury: Boolean(member.has_war_injury),
                        has_chronic_disease: Boolean(member.has_chronic_disease),
                        has_disability: Boolean(member.has_disability),
                        condition_type: member.condition_type || '',
                        health_notes: member.health_notes || ''
                    };
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.editingId = null;
                },

                confirmDelete(id, name) {
                    this.deletingId = id;
                    this.deletingName = name;
                    this.showDeleteModal = true;
                },

                async saveMember() {
                    this.saving = true;
                    const url = this.editingId ? `/citizen/members/${this.editingId}` : '/citizen/members';
                    const method = this.editingId ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.toastMessage = data.message;
                            this.toastType = 'success';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 3000);
                            this.closeModal();
                            window.location.reload();
                        } else {
                            alert(data.error || '{{ __('messages.error.general') }}');
                        }
                    } catch (error) {
                        alert('{{ __('messages.error.general') }}');
                    }

                    this.saving = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
