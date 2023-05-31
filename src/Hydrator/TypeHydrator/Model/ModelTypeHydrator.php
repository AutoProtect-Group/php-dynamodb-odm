<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;

class ModelTypeHydrator implements ModelTypeHydratorInterface
{
    public function hydrate(
        string $modelClassName,
        HydratorInterface $hydrator,
        string $fieldName,
        array $data,
    ): ModelInterface {
        return (clone $hydrator)
            ->setClassName($modelClassName)
            ->hydrate(
                $data,
                $fieldName
            )
        ;
    }
}
