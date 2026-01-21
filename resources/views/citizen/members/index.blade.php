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
                    {{ __('Manage Family Members') }}
                </h2>
            </div>
            <span class="text-sm text-gray-500">{{ $members->count() }} member(s)</span>
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
                    Add Member
                </button>
            </div>

            <!-- Members List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($members->count() > 0)
                        <div class="space-y-4">
                            @foreach($members as $member)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold {{ $member->gender === 'female' ? 'bg-pink-500' : 'bg-blue-500' }}">
                                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                                            <p class="text-sm text-gray-500 capitalize">{{ $member->relation_to_head }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right text-sm text-gray-500 hidden sm:block">
                                            @if($member->national_id)
                                                <p>ID: {{ $member->national_id }}</p>
                                            @endif
                                            @if($member->birth_date)
                                                <p>{{ $member->age }} years old</p>
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
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No Members Added</h3>
                            <p class="text-gray-500 mb-4">Add your family members to complete your household profile.</p>
                            <button 
                                @click="openAddModal"
                                class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition"
                            >
                                Add First Member
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="editingId ? 'Edit Member' : 'Add Member'"></h3>
                    
                    <form @submit.prevent="saveMember">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.full_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Relation <span class="text-red-500">*</span></label>
                                <select x-model="form.relation_to_head" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                    <option value="">-- Select --</option>
                                    @foreach($relations as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">National ID (Optional)</label>
                                <input type="text" x-model="form.national_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <select x-model="form.gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        <option value="">-- Select --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                                    <input type="date" x-model="form.birth_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700 transition disabled:opacity-50">
                                <span x-show="!saving" x-text="editingId ? 'Update' : 'Add Member'"></span>
                                <span x-show="saving">Saving...</span>
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
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Remove Member</h3>
                        <p class="text-sm text-gray-500 mb-4">Are you sure you want to remove <span class="font-medium" x-text="deletingName"></span>?</p>
                        
                        <div class="flex justify-center space-x-3">
                            <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <form :action="'/citizen/members/' + deletingId" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition">
                                    Remove
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
                    relation_to_head: '',
                    gender: '',
                    birth_date: ''
                },

                openAddModal() {
                    this.editingId = null;
                    this.form = { full_name: '', national_id: '', relation_to_head: '', gender: '', birth_date: '' };
                    this.showModal = true;
                },

                openEditModal(member) {
                    this.editingId = member.id;
                    this.form = {
                        full_name: member.full_name,
                        national_id: member.national_id || '',
                        relation_to_head: member.relation_to_head,
                        gender: member.gender || '',
                        birth_date: member.birth_date ? member.birth_date.split('T')[0] : ''
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
                            alert(data.error || 'An error occurred');
                        }
                    } catch (error) {
                        alert('An error occurred');
                    }
                    
                    this.saving = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
