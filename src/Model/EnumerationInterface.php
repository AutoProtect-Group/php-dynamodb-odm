<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model;

interface EnumerationInterface
{
    public function toScalar(): int|string;
}
