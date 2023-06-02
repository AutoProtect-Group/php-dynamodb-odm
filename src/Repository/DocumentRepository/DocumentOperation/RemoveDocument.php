<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;

/**
 * Class RemoveDocumentAttribute
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class RemoveDocument extends UpdateDocument
{
    /**
     * @return ModelInterface
     *
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function execute(): ModelInterface
    {
        $query = $this->repositoryContext
            ->getQueryBuilder()
            ->removeItemAttribute($this->getTableName())
            ->itemKey($this->getItemKey())
            ->removeAttributesByPath([$this->projectionAttrPath])
            ->getQuery();

        $this->repositoryContext
            ->getClient()
            ->update($this->primaryKey, $query);

        return $this->model;
    }
}
