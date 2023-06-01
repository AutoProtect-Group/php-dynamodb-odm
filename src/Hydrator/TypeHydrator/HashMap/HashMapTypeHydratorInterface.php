<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\HashMap;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;

interface HashMapTypeHydratorInterface
{

    public function hydrate(
        string $modelClassName,
        HydratorInterface $hydrator,
        string $fieldName,
        iterable $data,
    ): array;
}
