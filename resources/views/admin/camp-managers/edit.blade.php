<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">تعديل مدير مخيم</h2>
            <p class="mt-1 text-sm text-gray-500">تغيير المخيم أو الصلاحيات من هنا ينعكس مباشرة على ما يراه المدير في النظام.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                الحساب الحالي مرتبط بالمخيم: <span class="font-semibold">{{ $campManager->region?->name ?: 'غير محدد' }}</span>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.camp-managers.update', $campManager) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.camp-managers._form', [
                        'mode' => 'edit',
                        'submitLabel' => 'حفظ التعديلات',
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
