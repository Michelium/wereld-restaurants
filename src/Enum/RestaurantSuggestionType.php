<?php

namespace App\Enum;

enum RestaurantSuggestionType: string {

    case FIELDS = 'fields';
    case CLOSED = 'closed';
    case NEW = 'new';

    public function label(): string {
        return match ($this) {
            self::FIELDS => 'Data wijziging',
            self::CLOSED => 'Gesloten',
            self::NEW => 'Nieuw restaurant',
        };
    }

}
