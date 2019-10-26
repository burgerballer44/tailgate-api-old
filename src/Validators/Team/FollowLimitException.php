<?php

namespace TailgateApi\Validators\Team;

use Respect\Validation\Exceptions\ValidationException;

class FollowLimitException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Cannot join any more groups.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Must join more groups.',
        ]
    ];
}
