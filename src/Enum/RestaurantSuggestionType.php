<?php

namespace App\Enum;

enum RestaurantSuggestionType: string {

    case FIELDS = 'fields';
    case CLOSED = 'closed';

    public function label(): string {
        return match ($this) {
            self::FIELDS => 'Data wijziging',
            self::CLOSED => 'Gesloten',
        };
    }

}
