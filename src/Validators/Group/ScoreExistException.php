<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class ScoreExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Score does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Score exists.',
        ]
    ];
}
