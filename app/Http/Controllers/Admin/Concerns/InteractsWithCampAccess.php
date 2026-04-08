<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Distribution;
use App\Models\Household;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait InteractsWithCampAccess
{
    protected function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    protected function isCampManager(): bool
    {
        return $this->currentUser()->isCampManager();
    }

    protected function isAdmin(): bool
    {
        return $this->currentUser()->hasRole('admin');
    }

    protected function managedRegionId(): ?int
    {
        $managedRegionId = $this->currentUser()->managedRegionId();

        if ($this->isCampManager() && $managedRegionId === null) {
            abort(403, 'Camp manager account must be assigned to a camp region.');
        }

        return $managedRegionId;
    }

    protected function enforcedRegionId(?string $requestedRegionId = null): ?int
    {
        if (($managedRegionId = $this->managedRegionId()) !== null) {
            return $managedRegionId;
        }

        if ($requestedRegionId === null || $requestedRegionId === '') {
            return null;
        }

        return (int) $requestedRegionId;
    }

    protected function visibleCampRegionIds(): array
    {
        if (($managedRegionId = $this->managedRegionId()) !== null) {
            return [$managedRegionId];
        }

        return Region::query()
            ->allowedCamps()
            ->pluck('id')
            ->all();
    }

    protected function visibleCampRegionTree(): Collection
    {
        if (($managedRegionId = $this->managedRegionId()) !== null) {
            return Region::query()
                ->with([
                    'children' => function ($query) use ($managedRegionId) {
                        $query->whereKey($managedRegionId);
                    },
                ])
                ->whereNull('parent_id')
                ->whereHas('children', function ($query) use ($managedRegionId) {
                    $query->whereKey($managedRegionId);
                })
                ->get();
        }

        return Region::query()
            ->with(['children' => function ($query) {
                $query->allowedCamps();
            }])
            ->whereNull('parent_id')
            ->whereHas('children', function ($query) {
                $query->allowedCamps();
            })
            ->get();
    }

    protected function enforceHouseholdAccess(Household $household): void
    {
        $managedRegionId = $this->managedRegionId();

        if ($managedRegionId !== null && (int) $household->region_id !== $managedRegionId) {
            abort(403);
        }
    }

    protected function enforceDistributionAccess(Distribution $distribution): void
    {
        $distribution->loadMissing('household');

        if (! $distribution->household) {
            abort(404);
        }

        $this->enforceHouseholdAccess($distribution->household);
    }

    protected function denyCampManagers(): void
    {
        if ($this->isCampManager()) {
            abort(403);
        }
    }

    protected function denyUnlessAdmin(): void
    {
        if (! $this->isAdmin()) {
            abort(403);
        }
    }

    protected function ensureCan(string $permission): void
    {
        if (! $this->currentUser()->hasManagementPermission($permission)) {
            abort(403);
        }
    }

    protected function ensureAny(array $permissions): void
    {
        if (! $this->currentUser()->hasAnyManagementPermission($permissions)) {
            abort(403);
        }
    }

    protected function visibleHouseholdQuery()
    {
        return Household::query()->visibleTo($this->currentUser());
    }

    protected function visibleDistributionQuery()
    {
        return Distribution::query()->visibleTo($this->currentUser());
    }

    protected function isVisibleRegionFilterActive(Request $request): bool
    {
        return $this->enforcedRegionId($request->input('region_id')) !== null;
    }
}
