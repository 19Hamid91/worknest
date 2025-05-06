<?php

namespace App\Enums;

class Status
{
    public const PENDING = 'Pending';
    public const ONPROGRESS = 'On Progress';
    public const DONE = 'Done';

    public static function values(): array
    {
        return [
            self::PENDING,
            self::ONPROGRESS,
            self::DONE,
        ];
    }
}
