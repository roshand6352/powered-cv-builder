<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Pages\Concerns\SyncsRolePermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    use SyncsRolePermissions;

    protected static string $resource = RoleResource::class;
}
