<?php

declare(strict_types=1);

namespace He4rt\WidgetPlayer\Enums;

use Filament\Support\Contracts\HasLabel;

enum AnimationType: string implements HasLabel
{
    case Original = 'original';
    case Fade = 'fade';
    case SlideLeft = 'slide_left';
    case SlideRight = 'slide_right';
    case SlideTop = 'slide_top';
    case SlideBottom = 'slide_bottom';
    case Grow = 'grow';
    case Shrink = 'shrink';
    case SwingLeft = 'swing_left';
    case SwingRight = 'swing_right';
    case TiltRight = 'tilt_right';
    case TiltLeft = 'tilt_left';

    public function getLabel(): string
    {
        return match ($this) {
            self::Original => 'Original',
            self::Fade => 'Fade',
            self::SlideLeft => 'Slide Left',
            self::SlideRight => 'Slide Right',
            self::SlideTop => 'Slide Top',
            self::SlideBottom => 'Slide Bottom',
            self::Grow => 'Grow',
            self::Shrink => 'Shrink',
            self::SwingLeft => 'Swing Left',
            self::SwingRight => 'Swing Right',
            self::TiltRight => 'Tilt Right',
            self::TiltLeft => 'Tilt Left',
        };
    }
}
