<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Enums;

use Filament\Support\Contracts\HasLabel;

enum CoverStyle: string implements HasLabel
{
    case Square = 'square';
    case Canvas = 'canvas';
    case Vinyl = 'vinyl';
    case None = 'none';

    public function getLabel(): string
    {
        return match ($this) {
            self::Square => 'Square',
            self::Canvas => 'Canvas',
            self::Vinyl => 'Vinyl',
            self::None => 'None',
        };
    }
}
