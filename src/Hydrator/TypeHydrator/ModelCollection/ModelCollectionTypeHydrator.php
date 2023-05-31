<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ModelCollection;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model\ModelTypeHydratorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Traversable;

class ModelCollectionTypeHydrator implements ModelCollectionTypeHydratorInterface
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
    ): iterable {
        return array_map(
            fn(array $model): ModelInterface =>
            $this->modelTypeHydrator->hydrate($modelClassName, $hydrator, $fieldName, $model),
            $data instanceof Traversable ? iterator_to_array($data) : $data
        );
    }
}
