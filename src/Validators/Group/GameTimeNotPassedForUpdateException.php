<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class GameTimeNotPassedForUpdateException extends ValidationException
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
