<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Float;

class FloatTypeHydrator implements FloatTypeHydratorInterface
{
    public function hydrate(float|int|string $value): ?float
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) === false ? null : (float) $value;
    }
}
