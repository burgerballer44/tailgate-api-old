<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Exceptions\ValidationException;

class PendingUserExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Pending user does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Pending user exists.',
        ]
    ];
}
