<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date;

use DateTime;
use DateTimeInterface;
use Exception;

class DateTypeHydrator implements DateTypeHydratorInterface
{

    public function hydrate(string $value): DateTimeInterface
    {
        try {
            return new DateTime($value);
        } catch (Exception) {
            return new DateTime('@' . static::INVALID_TIMESTAMP);
        }
    }
}
