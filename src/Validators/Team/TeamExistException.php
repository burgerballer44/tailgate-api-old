<?php

namespace TailgateApi\Validators\Team;

use Respect\Validation\Exceptions\ValidationException;

class TeamExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Team does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Team exists.',
        ]
    ];
}
