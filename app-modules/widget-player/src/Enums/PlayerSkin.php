<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PlayerSkin: string implements HasColor, HasLabel
{
    case Compact = 'compact';
    case Boxy = 'boxy';
    case Gallery = 'gallery';
    case Minimal = 'minimal';
    case MacOS = 'macos';
    case Shell = 'shell';
    case Discord = 'discord';

    public function getLabel(): string
    {
        return match ($this) {
            self::Compact => 'Compact',
            self::Boxy => 'Boxy',
            self::Gallery => 'Gallery',
            self::Minimal => 'Minimal',
            self::MacOS => 'macOS',
            self::Shell => 'Shell',
            self::Discord => 'Discord',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Compact => 'primary',
            self::Boxy => 'info',
            self::Gallery => 'success',
            self::Minimal => 'gray',
            self::MacOS => 'warning',
            self::Shell => 'danger',
            self::Discord => 'info',
        };
    }
}
