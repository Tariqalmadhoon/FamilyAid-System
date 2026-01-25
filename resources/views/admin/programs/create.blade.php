<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.programs.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('messages.programs.new') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form method="POST" action="{{ route('admin.programs.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.program.name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.program.description') }}</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.program.start_date') }}</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.program.end_date') }}</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span class="ml-2 text-sm text-gray-700">{{ __('messages.program.active_hint') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="allow_multiple" value="1" {{ old('allow_multiple') ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <span class="ml-2 text-sm text-gray-700">{{ __('messages.program.allow_multiple') }}</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.programs.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('messages.actions.cancel') }}</a>
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700">{{ __('messages.actions.create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
