<?php

namespace App\Enums;

enum GuardNames: string
{
    case API = 'api';
    case WEB = 'web';

    public static function options(): array
    {
        $cases = self::cases();
        $options = [];
        foreach ($cases as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }
}
