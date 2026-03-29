<?php

declare(strict_types=1);

namespace He4rt\Identity\ExternalIdentity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IdentityType: string implements HasColor, HasLabel
{
    case External = 'external';
    case Music = 'music';

    public function getLabel(): string
    {
        return match ($this) {
            self::External => 'External Platform',
            self::Music => 'Music',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::External => 'primary',
            self::Music => 'success',
        };
    }
}
