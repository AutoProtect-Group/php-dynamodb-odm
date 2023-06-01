<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures\enums;

use Autoprotect\DynamodbODM\Model\EnumerationInterface;

enum BackedEnumeration: string implements EnumerationInterface
{
    case ONE_VALUE = 'one-value';
    case SECOND_VALUE = 'second-value';

    public function toScalar(): int|string
    {
        return $this->value;
    }
}
