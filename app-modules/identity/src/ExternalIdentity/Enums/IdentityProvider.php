<?php

declare(strict_types=1);

namespace He4rt\Identity\ExternalIdentity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IdentityProvider: string implements HasColor, HasLabel
{
    case GitHub = 'github';
    case Spotify = 'spotify';

    public function getLabel(): string
    {
        return match ($this) {
            self::GitHub => 'GitHub',
            self::Spotify => 'Spotify',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::GitHub => 'warning',
            self::Spotify => 'success',
        };
    }

    public function getType(): IdentityType
    {
        return match ($this) {
            self::GitHub => IdentityType::External,
            self::Spotify => IdentityType::External,
        };
    }

    public function getSocialiteDriverName(): string
    {
        return match ($this) {
            self::GitHub => 'github',
            self::Spotify => 'spotify',
        };
    }
}
