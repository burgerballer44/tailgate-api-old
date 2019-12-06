<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class FollowLimitException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Cannot follow any more teams.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Must follow more teams.',
        ]
    ];
}
