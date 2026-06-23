<?php

namespace App\Filament\Forms;

use App\Enums\PermissionName;
use Filament\Forms;

class PermissionCheckboxGroups
{
    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function schema(): array
    {
        $fieldsets = collect(PermissionName::groupedByModule())
            ->map(function (array $permissions, string $module) {
                $options = collect($permissions)
                    ->mapWithKeys(fn (PermissionName $permission) => [
                        $permission->value => $permission->label(),
                    ])
                    ->all();

                return Forms\Components\Fieldset::make($module)
                    ->schema([
                        Forms\Components\CheckboxList::make(self::fieldKey($module))
                            ->label(false)
                            ->options($options)
                            ->columns(2)
                            ->bulkToggleable(),
                    ]);
            })
            ->values()
            ->all();

        return [
            Forms\Components\Section::make('Permissions')
                ->description('Select permissions grouped by module. Changes sync when the role is saved.')
                ->schema($fieldsets)
                ->columnSpanFull(),
        ];
    }

    public static function fieldKey(string $module): string
    {
        return 'permission_names.'.str($module)->snake()->toString();
    }

    /**
     * @return list<string>
     */
    public static function moduleKeys(): array
    {
        return collect(PermissionName::groupedByModule())
            ->keys()
            ->map(fn (string $module) => self::fieldKey($module))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function flattenSelected(array $data): array
    {
        $selected = [];

        foreach (self::moduleKeys() as $key) {
            $values = $data[$key] ?? [];

            if (is_array($values)) {
                $selected = [...$selected, ...$values];
            }
        }

        return array_values(array_unique(array_filter($selected)));
    }

    /**
     * @param  list<string>  $permissionNames
     * @return array<string, list<string>>
     */
    public static function hydrateByModule(array $permissionNames): array
    {
        $data = [];

        foreach (PermissionName::groupedByModule() as $module => $permissions) {
            $moduleValues = array_map(fn (PermissionName $p) => $p->value, $permissions);
            $data[self::fieldKey($module)] = array_values(array_intersect($permissionNames, $moduleValues));
        }

        return $data;
    }
}
