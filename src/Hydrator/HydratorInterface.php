<?php

namespace Autoprotect\DynamodbODM\Hydrator;

use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Interface HydratorInterface
 *
 * @package Autoprotect\DynamodbODM\Hydrator
 */
interface HydratorInterface
{
    /**
     * Hydrate the model with the data from the DB
     *
     * @param array $data
     * @param string|null $discriminatorFieldName
     * @param ModelInterface|null $existingModel
     *
     * @return ModelInterface
     */
    public function hydrate(
        array $data,
        ?string $discriminatorFieldName = null,
        ?ModelInterface $existingModel = null
    ): ModelInterface;

    /**
     * Reset class name in order to make the hydrator suitable for hydrating another model
     *
     * @param string $modelClassName
     *
     * @return $this
     */
    public function setClassName(string $modelClassName): static;
}
