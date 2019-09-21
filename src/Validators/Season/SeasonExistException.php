<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Exceptions\ValidationException;

class SeasonExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Season does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Season exists.',
        ]
    ];
}
