<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Exceptions\ValidationException;

class UserExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'User does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'User exists.',
        ]
    ];
}
