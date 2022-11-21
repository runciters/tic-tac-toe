<?php

namespace App\Enum;

enum Position: int
{
    case One = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;
    case Six = 6;
    case Seven = 7;
    case Eight = 8;
    case Nine = 9;

    public function getCoordinates(): array
    {
        return match($this)
        {
            self::One => [0, 0],
            self::Two => [1, 0],
            self::Three => [2, 0],
            self::Four => [0, 1],
            self::Five => [1, 1],
            self::Six => [2, 1],
            self::Seven => [0, 2],
            self::Eight => [1, 2],
            self::Nine => [2, 2],
        };
    }
}