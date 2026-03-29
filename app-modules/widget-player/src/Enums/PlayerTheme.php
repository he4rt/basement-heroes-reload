<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlayerTheme: string implements HasLabel
{
    case Dark = 'dark';
    case Light = 'light';

    public function getLabel(): string
    {
        return match ($this) {
            self::Dark => 'Dark Mode',
            self::Light => 'Light Mode',
        };
    }
}
