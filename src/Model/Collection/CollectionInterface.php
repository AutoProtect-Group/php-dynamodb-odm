<?php

namespace Autoprotect\DynamodbODM\Model\Collection;

use Closure;
use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Interface CollectionInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Model\Collection
 */
interface CollectionInterface extends \IteratorAggregate
{
    /**
     * @param $id
     *
     * @return ModelInterface|null
     */
    public function getModel($id): ?ModelInterface;

    /**
     * @return array
     */
    public function getModels(): array;

    /**
     * @param ModelInterface $model
     *
     * @return CollectionInterface
     */
    public function addModel(ModelInterface $model): CollectionInterface;

    /**
     * @param Closure $closure
     *
     * @return CollectionInterface
     */
    public function sort(Closure $closure): CollectionInterface;
}
