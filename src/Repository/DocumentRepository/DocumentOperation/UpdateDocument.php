<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;

/**
 * Class UpdateDocument
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class UpdateDocument extends AbstractAttributeOperation
{
    /**
     * @var ModelInterface
     */
    protected ModelInterface $model;

    /**
     * @param ModelInterface $model
     *
     * @return $this
     */
    public function withModel(ModelInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return ModelInterface
     * @throws \Exception
     */
    public function execute(): ModelInterface
    {
        $query = $this->repositoryContext
            ->getQueryBuilder()
            ->updateItem($this->getTableName())
            ->itemKey($this->getItemKey())
            ->attributes([
                $this->projectionAttrPath => $this->getModelData()
            ])
            ->getQuery();

        $item = $this->repositoryContext
            ->getClient()
            ->update($this->primaryKey, $query);

        if (!$item) {
            throw new EntityNotFoundException();
        }

        $dotOutput = dot($this->repositoryContext->getMarshaler()->unmarshalItem($item))
            ->get($this->projectionAttrPath);

        return $this->repositoryContext
            ->getHydrator()
            ->hydrate($dotOutput);
    }

    /**
     * @return array
     */
    private function getModelData(): array
    {
        return $this->repositoryContext
            ->getSerializer()
            ->serialize($this->model);
    }
}
