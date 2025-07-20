<?php

namespace App\Enum;

enum RestaurantSuggestionStatus: string {

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Wacht op goedkeuring',
            self::APPROVED => 'Goedgekeurd',
            self::REJECTED => 'Afgewezen',
        };
    }

}
