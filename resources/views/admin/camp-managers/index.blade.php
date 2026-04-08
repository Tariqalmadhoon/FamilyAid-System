<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">مديرو المخيمات</h2>
                <p class="mt-1 text-sm text-gray-500">من هنا يحدد السوبر أدمن المخيم المرتبط بكل مدير والصلاحيات الممنوحة له داخل ذلك المخيم.</p>
            </div>
            <a href="{{ route('admin.camp-managers.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 text-sm font-medium text-white hover:bg-teal-700">
                إضافة مدير مخيم
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">المدير</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">الهوية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">المخيم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">الصلاحيات</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($campManagers as $campManager)
                                @php
                                    $effectivePermissions = $campManager->effectiveCampManagerPermissions();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $campManager->full_name ?: $campManager->name }}</div>
                                        <div class="mt-1 text-sm text-gray-500">{{ $campManager->phone ?: 'لا يوجد رقم هاتف' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $campManager->national_id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $campManager->region?->name ?: 'غير مرتبط بمخيم' }}</td>
                                    <td class="px-6 py-4">
                                        @if($campManager->camp_permissions_configured)
                                            <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-xs font-medium text-teal-800">مهيأ من السوبر أدمن</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">الصلاحيات الافتراضية القديمة</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($effectivePermissions as $permission)
                                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                                                    {{ $permissionLabels[$permission] ?? $permission }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-400">لا توجد صلاحيات معروضة</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <a href="{{ route('admin.camp-managers.edit', $campManager) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            تعديل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">لا يوجد مديرو مخيمات بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($campManagers->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $campManagers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
