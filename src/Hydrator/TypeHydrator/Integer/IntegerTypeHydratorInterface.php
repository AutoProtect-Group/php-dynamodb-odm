<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Integer;

interface IntegerTypeHydratorInterface
{
    public function hydrate(string|int|float $value): ?int;
}
