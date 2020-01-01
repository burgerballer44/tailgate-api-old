<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Exceptions\ValidationException;

class GameTimeNotPassedException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The start of the game has passed.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The start of the game has not passed.',
        ]
    ];
}
