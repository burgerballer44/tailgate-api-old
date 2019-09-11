<?php

namespace TailgateApi\Validators\Player;

use Respect\Validation\Exceptions\ValidationException;

class UniqueUsernameException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Please choose a unique username. This username is unavailable.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Please choose a... not unique username.',
        ]
    ];
}
