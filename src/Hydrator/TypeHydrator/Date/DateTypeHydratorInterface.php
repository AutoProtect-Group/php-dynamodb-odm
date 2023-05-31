<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date;

use DateTimeInterface;

interface DateTypeHydratorInterface
{
    /*
     * This is the timestamp for 0000-00-00 00:00:00 DateTime. We consider this as an indicator of a wrong date
     */
    public const INVALID_TIMESTAMP = -62169984000;

    public function hydrate(string $value): DateTimeInterface;
}
