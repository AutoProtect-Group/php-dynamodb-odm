<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures\enums;

use Autoprotect\DynamodbODM\Model\UnitEnumerationInterface;
use Henzeb\Enumhancer\Concerns\From;

enum UnitEnumeration implements UnitEnumerationInterface
{
    use From;

    case FIRST_VALUE;
    case SECOND_VALUE;

    public function toScalar(): string
    {
        return $this->name;
    }
}
