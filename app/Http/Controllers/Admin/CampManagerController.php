<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\InteractsWithCampAccess;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CampManagerController extends Controller
{
    use InteractsWithCampAccess;

    public function index(): View
    {
        $this->denyUnlessAdmin();
        $this->ensureCampManagerRoleDefinition();

        $campManagers = User::query()
            ->role('camp_manager')
            ->with(['region', 'permissions'])
            ->latest()
            ->paginate(20);

        return view('admin.camp-managers.index', [
            'campManagers' => $campManagers,
            'permissionLabels' => $this->permissionLabels(),
        ]);
    }

    public function create(): View
    {
        $this->denyUnlessAdmin();
        $this->ensureCampManagerRoleDefinition();

        return view('admin.camp-managers.create', [
            'campManager' => new User(['is_staff' => true]),
            'regions' => $this->visibleCampRegionTree(),
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->denyUnlessAdmin();
        $this->ensureCampManagerRoleDefinition();

        $validated = $request->validate($this->rules(), [], $this->attributes());

        $campManager = User::create([
            'name' => trim((string) $validated['name']),
            'national_id' => $validated['national_id'],
            'phone' => trim((string) ($validated['phone'] ?? '')) ?: null,
            'password' => Hash::make($validated['password']),
            'is_staff' => true,
            'region_id' => (int) $validated['region_id'],
            'camp_permissions_configured' => true,
        ]);

        $campManager->assignRole('camp_manager');
        $campManager->syncPermissions($validated['permissions']);

        AuditLog::log('create_camp_manager', 'User', $campManager->id, null, [
            'name' => $campManager->name,
            'national_id' => $campManager->national_id,
            'region_id' => $campManager->region_id,
            'permissions' => $campManager->getDirectPermissions()->pluck('name')->values()->all(),
        ]);

        return redirect()->route('admin.camp-managers.index')
            ->with('success', 'تم إنشاء مدير المخيم وربط صلاحياته بنجاح.');
    }

    public function edit(User $campManager): View
    {
        $this->denyUnlessAdmin();
        $this->ensureCampManagerRoleDefinition();
        $this->ensureCampManagerUser($campManager);

        return view('admin.camp-managers.edit', [
            'campManager' => $campManager->load(['region', 'permissions']),
            'regions' => $this->visibleCampRegionTree(),
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => $campManager->effectiveCampManagerPermissions()->all(),
        ]);
    }

    public function update(Request $request, User $campManager): RedirectResponse
    {
        $this->denyUnlessAdmin();
        $this->ensureCampManagerRoleDefinition();
        $this->ensureCampManagerUser($campManager);

        $validated = $request->validate($this->rules($campManager), [], $this->attributes());

        $before = [
            'name' => $campManager->name,
            'national_id' => $campManager->national_id,
            'phone' => $campManager->phone,
            'region_id' => $campManager->region_id,
            'camp_permissions_configured' => $campManager->camp_permissions_configured,
            'permissions' => $campManager->effectiveCampManagerPermissions()->all(),
        ];

        $campManager->fill([
            'name' => trim((string) $validated['name']),
            'national_id' => $validated['national_id'],
            'phone' => trim((string) ($validated['phone'] ?? '')) ?: null,
            'is_staff' => true,
            'region_id' => (int) $validated['region_id'],
            'camp_permissions_configured' => true,
        ]);

        if (! empty($validated['password'])) {
            $campManager->password = Hash::make($validated['password']);
        }

        $campManager->save();
        $campManager->syncRoles(['camp_manager']);
        $campManager->syncPermissions($validated['permissions']);

        AuditLog::log('update_camp_manager', 'User', $campManager->id, $before, [
            'name' => $campManager->name,
            'national_id' => $campManager->national_id,
            'phone' => $campManager->phone,
            'region_id' => $campManager->region_id,
            'camp_permissions_configured' => $campManager->camp_permissions_configured,
            'permissions' => $campManager->getDirectPermissions()->pluck('name')->values()->all(),
        ]);

        return redirect()->route('admin.camp-managers.index')
            ->with('success', 'تم تحديث مدير المخيم وصلاحياته بنجاح.');
    }

    private function ensureCampManagerUser(User $user): void
    {
        if (! $user->isCampManager()) {
            abort(404);
        }
    }

    private function rules(?User $campManager = null): array
    {
        $regionIds = Region::query()->allowedCamps()->pluck('id')->all();
        $permissions = User::configurableCampManagerPermissions();

        $passwordRules = ['required', 'confirmed', 'string', 'min:8'];

        if ($campManager) {
            $passwordRules = ['nullable', 'confirmed', 'string', 'min:8'];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'digits:9', Rule::unique('users', 'national_id')->ignore($campManager?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $passwordRules,
            'region_id' => ['required', Rule::in($regionIds)],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', Rule::in($permissions)],
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'اسم المدير',
            'national_id' => 'رقم الهوية',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'region_id' => 'المخيم',
            'permissions' => 'الصلاحيات',
        ];
    }

    private function permissionGroups(): array
    {
        return [
            [
                'title' => 'الأسر',
                'permissions' => [
                    'households.view' => 'عرض الأسر',
                    'households.create' => 'إضافة أسرة',
                    'households.update' => 'تعديل الأسر',
                    'households.delete' => 'حذف الأسر',
                    'households.verify' => 'اعتماد الأسر',
                    'households.import' => 'استيراد الأسر',
                    'households.export' => 'تصدير الأسر',
                ],
            ],
            [
                'title' => 'الأفراد',
                'permissions' => [
                    'members.view' => 'عرض الأفراد',
                    'members.create' => 'إضافة الأفراد',
                    'members.update' => 'تعديل الأفراد',
                    'members.delete' => 'حذف الأفراد',
                ],
            ],
            [
                'title' => 'التوزيعات',
                'permissions' => [
                    'distributions.view' => 'عرض التوزيعات',
                    'distributions.create' => 'تسجيل توزيع',
                    'distributions.delete' => 'حذف توزيع',
                    'distributions.export' => 'تصدير التوزيعات',
                ],
            ],
        ];
    }

    private function permissionLabels(): array
    {
        return collect($this->permissionGroups())
            ->flatMap(fn (array $group) => $group['permissions'])
            ->all();
    }

    private function ensureCampManagerRoleDefinition(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = User::configurableCampManagerPermissions();

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('camp_manager', 'web')
            ->syncPermissions($permissions);
    }
}