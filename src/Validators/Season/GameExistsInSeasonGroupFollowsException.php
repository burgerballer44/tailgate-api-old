<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Exceptions\ValidationException;

class GameExistsInSeasonGroupFollowsException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Game is not followed by group.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Game is followed by group.',
        ]
    ];
}
