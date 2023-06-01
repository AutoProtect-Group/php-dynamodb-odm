<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Float;

interface FloatTypeHydratorInterface
{
    public function hydrate(string|int|float $value): ?float;
}
