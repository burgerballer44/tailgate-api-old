<?php

namespace TailgateApi\Validators\Season;

use Respect\Validation\Exceptions\ValidationException;

class SeasonNotEndedException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Season has ended.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Season has not ended.',
        ]
    ];
}
