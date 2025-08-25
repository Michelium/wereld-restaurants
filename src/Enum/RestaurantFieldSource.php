<?php

namespace App\Enum;

enum RestaurantFieldSource: string {

    case OSM = 'osm';     // OpenStreetMap data
    case USER = 'user';   // Via Restaurant Suggestion
    case ADMIN = 'admin'; // Via EasyAdmin interface

}
