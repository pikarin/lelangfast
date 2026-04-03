<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AuctionStatus: string implements HasColor, HasLabel
{
    case Upcoming = 'upcoming';
    case Active = 'active';
    case Ended = 'ended';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Upcoming => 'Upcoming',
            self::Active => 'Active',
            self::Ended => 'Ended',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Upcoming => 'info',
            self::Active => 'success',
            self::Ended => 'gray',
            self::Cancelled => 'danger',
        };
    }
}
