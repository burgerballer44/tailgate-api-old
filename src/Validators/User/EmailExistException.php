<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Exceptions\ValidationException;

class EmailExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Email does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Email exists.',
        ]
    ];
}
