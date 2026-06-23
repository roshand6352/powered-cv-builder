<?php

namespace App\Filament\Resources\RoleResource\Pages\Concerns;

use App\Filament\Forms\PermissionCheckboxGroups;
use Spatie\Permission\Models\Role;

trait SyncsRolePermissions
{
    /** @var list<string> */
    protected array $permissionNames = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionNames = PermissionCheckboxGroups::flattenSelected($data);

        foreach (PermissionCheckboxGroups::moduleKeys() as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissionNames = PermissionCheckboxGroups::flattenSelected($data);

        foreach (PermissionCheckboxGroups::moduleKeys() as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncPermissions();
    }

    protected function afterSave(): void
    {
        $this->syncPermissions();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Role|null $record */
        $record = $this->record;

        return [
            ...$data,
            ...PermissionCheckboxGroups::hydrateByModule(
                $record?->permissions()->pluck('name')->all() ?? [],
            ),
        ];
    }

    protected function syncPermissions(): void
    {
        if (! $this->record instanceof Role) {
            return;
        }

        $this->record->syncPermissions($this->permissionNames);
    }
}
