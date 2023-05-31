<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Hydrator\fixtures\enums;

use Autoprotect\DynamodbODM\Model\UnitEnumerationInterface;
use Henzeb\Enumhancer\Concerns\From;

enum ApplicationStatus implements UnitEnumerationInterface
{
    use From;

    case NEW;
    case FINISHED;

    public function toScalar(): string
    {
        return $this->name;
    }
}
