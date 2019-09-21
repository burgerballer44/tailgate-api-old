<?php

namespace TailgateApi\Validators\Group;

use Respect\Validation\Exceptions\ValidationException;

class MemberExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Member does not exist.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Member exists.',
        ]
    ];
}
