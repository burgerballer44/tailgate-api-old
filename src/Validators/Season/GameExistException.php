<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Exceptions\ValidationException;

class GameExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Game does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Game exists.',
        ]
    ];
}
