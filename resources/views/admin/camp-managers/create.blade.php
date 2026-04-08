<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">إضافة مدير مخيم</h2>
            <p class="mt-1 text-sm text-gray-500">أنشئ حساباً إدارياً مربوطاً بمخيم واحد، ثم اختر ما يستطيع الوصول إليه داخل ذلك المخيم.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.camp-managers.store') }}">
                    @csrf
                    @include('admin.camp-managers._form', [
                        'mode' => 'create',
                        'submitLabel' => 'حفظ المدير',
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
