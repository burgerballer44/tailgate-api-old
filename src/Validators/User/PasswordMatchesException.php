<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Exceptions\ValidationException;

class PasswordMatchesException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Password confirmation does not match.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Password confirmation matches.',
        ]
    ];
}
