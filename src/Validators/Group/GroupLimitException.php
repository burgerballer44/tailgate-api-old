<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class GroupLimitException extends ValidationException
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
