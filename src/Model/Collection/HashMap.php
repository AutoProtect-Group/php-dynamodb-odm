<?php

namespace Autoprotect\DynamodbODM\Model\Collection;

use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Class HashMap
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Model\Collection
 */
class HashMap extends ModelCollection
{
    /**
     * @param ModelInterface $model
     *
     * @return HashMap
     */
    public function addModel(ModelInterface $model): self
    {
        $this->models[$model->getId()] = $model;

        return $this;
    }
}
