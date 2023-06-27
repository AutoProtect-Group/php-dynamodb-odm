<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Collection;

use ArrayIterator;
use Closure;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Traversable;

/**
 * Class Collection
 *
 * @package Autoprotect\DynamodbODM\Model
 */
class ModelCollection implements CollectionInterface
{
    /**
     * @var array
     */
    protected array $models = [];

    /**
     * Initializes a new ModelCollection.
     *
     * @param array $models
     */
    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    /**
     * @param int|string $id
     *
     * @return ModelInterface
     */
    public function getModel($id): ?ModelInterface
    {
        if (!isset($this->models[$id])) {
            return null;
        }

        return $this->models[$id];
    }

    /**
     * @return array
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * @param ModelInterface $model
     *
     * @return self
     */
    public function addModel(ModelInterface $model): self
    {
        $this->models[] = $model;

        return $this;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->models);
    }

    /**
     * @inheritDoc
     */
    public function sort(Closure $closure): CollectionInterface
    {
        usort($this->models, $closure);

        return $this;
    }
}
