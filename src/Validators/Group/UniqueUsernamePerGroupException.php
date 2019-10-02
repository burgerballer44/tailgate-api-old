<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class UniqueUsernamePerGroupException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Please choose a unique username for this group. This username is unavailable.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Please choose a... not unique username.',
        ]
    ];
}
