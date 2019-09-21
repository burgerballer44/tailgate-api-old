<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class GroupExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Group does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Group exists.',
        ]
    ];
}
