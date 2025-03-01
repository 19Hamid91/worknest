<?php

namespace App\Enums;

class Role
{
    public const SUPERADMIN = 'Superadmin';
    public const PROJECT_MANAGER = 'Project Manager';
    public const USER = 'User';

    public static function values(): array
    {
        return [
            self::SUPERADMIN,
            self::PROJECT_MANAGER,
            self::USER,
        ];
    }
}
