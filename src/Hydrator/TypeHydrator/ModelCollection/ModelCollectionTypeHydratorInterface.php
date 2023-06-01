<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ModelCollection;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Traversable;

interface ModelCollectionTypeHydratorInterface
{
    /**
     * Hydrate a collection of models
     *
     * @param string            $modelClassName
     * @param HydratorInterface $hydrator
     * @param string            $fieldName // model field name
     * @param array             $data
     *
     * @return iterable
     */
    public function hydrate(
        string $modelClassName,
        HydratorInterface $hydrator,
        string $fieldName,
        iterable $data,
    ): iterable;
}
