<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model;

use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;

interface ModelTypeHydratorInterface
{
    /**
     * Hydrate model
     *
     * @param string            $modelClassName
     * @param HydratorInterface $hydrator
     * @param string            $fieldName // model field name
     * @param array             $data
     *
     * @return ModelInterface
     */
    public function hydrate(
        string $modelClassName,
        HydratorInterface $hydrator,
        string $fieldName,
        array $data,
    ): ModelInterface;
}
