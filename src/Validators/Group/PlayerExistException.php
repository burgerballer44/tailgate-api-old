<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class PlayerExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Player does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Player exists.',
        ]
    ];
}
