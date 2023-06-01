<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures\enums;

use Autoprotect\DynamodbODM\Model\UnitEnumerationInterface;
use Henzeb\Enumhancer\Concerns\From;

enum CustomerType implements UnitEnumerationInterface
{
    use From;

    case PRIVATE;
    case BUSINESS;

    public function toScalar(): string
    {
        return $this->name;
    }
}
