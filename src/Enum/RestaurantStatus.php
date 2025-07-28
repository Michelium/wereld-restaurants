<?php

namespace App\Enum;

enum RestaurantStatus: string {

    case OPEN = 'open';
    case CLOSED = 'closed';
    case TEMPORARILY_CLOSED = 'temporarily_closed';

    public function label(): string {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Permanent gesloten',
            self::TEMPORARILY_CLOSED => 'Tijdelijk gesloten',
        };
    }

    public static function choiceList(): array
    {
        return array_combine(
            array_map(fn($status) => $status->label(), self::cases()),
            self::cases()
        );
    }

}
