@php
    $selectedPermissions = collect(old('permissions', $selectedPermissions ?? []))
        ->filter()
        ->values()
        ->all();
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">اسم المدير</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $campManager->name) }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
            <input
                id="national_id"
                name="national_id"
                type="text"
                inputmode="numeric"
                value="{{ old('national_id', $campManager->national_id) }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                required
            >
            @error('national_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
            <input
                id="phone"
                name="phone"
                type="text"
                value="{{ old('phone', $campManager->phone) }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            >
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">المخيم</label>
            <select
                id="region_id"
                name="region_id"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                required
            >
                <option value="">اختر المخيم</option>
                @foreach($regions as $region)
                    <optgroup label="{{ $region->name }}">
                        @foreach($region->children as $child)
                            <option value="{{ $child->id }}" @selected((string) old('region_id', $campManager->region_id) === (string) $child->id)>
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

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                كلمة المرور
                @if($mode === 'edit')
                    <span class="text-xs text-gray-400">(اتركها فارغة للإبقاء على الحالية)</span>
                @endif
            </label>
            <input
                id="password"
                name="password"
                type="password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                {{ $mode === 'create' ? 'required' : '' }}
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                {{ $mode === 'create' ? 'required' : '' }}
            >
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-5 py-4">
            <h3 class="text-base font-semibold text-gray-900">صلاحيات مدير المخيم</h3>
            <p class="mt-1 text-sm text-gray-500">الصلاحيات المختارة هنا هي التي ستتحكم في ما يراه ويفعله المدير داخل مخيمه فقط.</p>
        </div>

        <div class="p-5 space-y-5">
            @foreach($permissionGroups as $group)
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $group['title'] }}</h4>
                        <span class="text-xs text-gray-400">{{ count($group['permissions']) }} صلاحية</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($group['permissions'] as $permission => $label)
                            <label class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-3 hover:border-teal-300">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission }}"
                                    @checked(in_array($permission, $selectedPermissions, true))
                                    class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                >
                                <span>
                                    <span class="block text-sm font-medium text-gray-800">{{ $label }}</span>
                                    <span class="block text-xs text-gray-400">{{ $permission }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @error('permissions')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('permissions.*')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.camp-managers.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
            رجوع
        </a>
        <button type="submit" class="px-5 py-2 rounded-lg bg-teal-600 text-sm font-medium text-white hover:bg-teal-700">
            {{ $submitLabel }}
        </button>
    </div>
</div>
