<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\HashMap;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model\ModelTypeHydratorInterface;

class HashMapTypeHydrator implements HashMapTypeHydratorInterface
{
    public function __construct(
        protected ModelTypeHydratorInterface $modelTypeHydrator,
    ) {
    }

    public function hydrate(
        string $modelClassName,
        HydratorInterface $hydrator,
        string $fieldName,
        iterable $data,
    ): array {
        $processedValue = [];
        foreach ($data as $key => $model) {
            $processedValue[$key] = $this->modelTypeHydrator->hydrate($modelClassName, $hydrator, $fieldName, $model);
        }
        return $processedValue;
    }
}
