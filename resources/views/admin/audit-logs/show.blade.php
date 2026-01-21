<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log Details</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <dl class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Timestamp</dt>
                        <dd class="col-span-2 font-medium">{{ $log->created_at->format('F j, Y H:i:s') }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">User</dt>
                        <dd class="col-span-2 font-medium">{{ $log->user->name ?? 'System' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Action</dt>
                        <dd class="col-span-2">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($log->action === 'create') bg-green-100 text-green-800
                                @elseif($log->action === 'update') bg-blue-100 text-blue-800
                                @elseif($log->action === 'delete') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($log->action) }}
                            </span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Entity</dt>
                        <dd class="col-span-2 font-medium">{{ $log->entity_type }} #{{ $log->entity_id }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">IP Address</dt>
                        <dd class="col-span-2 font-medium">{{ $log->ip_address }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">User Agent</dt>
                        <dd class="col-span-2 text-sm text-gray-600 break-all">{{ $log->user_agent }}</dd>
                    </div>
                </dl>

                @if($log->before_data || $log->after_data)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="font-medium text-gray-900 mb-4">Data Changes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($log->before_data)
                                <div>
                                    <p class="text-sm font-medium text-red-600 mb-2">Before</p>
                                    <pre class="bg-red-50 p-3 rounded text-xs overflow-auto max-h-64">{{ json_encode($log->before_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                            @if($log->after_data)
                                <div>
                                    <p class="text-sm font-medium text-green-600 mb-2">After</p>
                                    <pre class="bg-green-50 p-3 rounded text-xs overflow-auto max-h-64">{{ json_encode($log->after_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
