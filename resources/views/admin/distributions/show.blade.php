<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.distributions.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Distribution Details</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <dl class="space-y-4">
                    <div class="grid grid-cols-2">
                        <dt class="text-sm text-gray-500">Household</dt>
                        <dd class="font-medium">
                            <a href="{{ route('admin.households.show', $distribution->household_id) }}" class="text-teal-600 hover:text-teal-800">{{ $distribution->household->head_name ?? 'Unknown' }}</a>
                        </dd>
                    </div>
                    <div class="grid grid-cols-2">
                        <dt class="text-sm text-gray-500">National ID</dt>
                        <dd class="font-medium">{{ $distribution->household->head_national_id ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-2">
                        <dt class="text-sm text-gray-500">Program</dt>
                        <dd class="font-medium">{{ $distribution->aidProgram->name ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-2">
                        <dt class="text-sm text-gray-500">Date</dt>
                        <dd class="font-medium">{{ $distribution->distribution_date->format('F j, Y') }}</dd>
                    </div>
                    <div class="grid grid-cols-2">
                        <dt class="text-sm text-gray-500">Recorded By</dt>
                        <dd class="font-medium">{{ $distribution->distributor->name ?? 'System' }}</dd>
                    </div>
                    @if($distribution->notes)
                        <div class="grid grid-cols-2">
                            <dt class="text-sm text-gray-500">Notes</dt>
                            <dd class="font-medium">{{ $distribution->notes }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 pt-6 border-t flex justify-between">
                    <form action="{{ route('admin.distributions.destroy', $distribution) }}" method="POST" onsubmit="return confirm('Delete this distribution?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                    </form>
                    <a href="{{ route('admin.distributions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
