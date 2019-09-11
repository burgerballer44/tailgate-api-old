<?php

namespace TailgateApi\Validators\User;

use Respect\Validation\Exceptions\ValidationException;

class UniqueEmailException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Please choose a unique email. This email is unavailable.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Please choose a... not unique email.',
        ]
    ];
}
