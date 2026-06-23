<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Support = 'support';
    case Editor = 'editor';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Support => 'Support',
            self::Editor => 'Editor',
        };
    }

    /**
     * @return list<string>
     */
    public static function staffRoles(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<string>
     */
    public static function panelRoles(): array
    {
        return self::staffRoles();
    }
}
