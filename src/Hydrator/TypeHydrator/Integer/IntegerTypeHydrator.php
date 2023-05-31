<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Integer;

class IntegerTypeHydrator implements IntegerTypeHydratorInterface
{
    public function hydrate(float|int|string $value): ?int
    {
        return filter_var($value, FILTER_VALIDATE_INT) === false ? null : (int) $value;
    }
}
