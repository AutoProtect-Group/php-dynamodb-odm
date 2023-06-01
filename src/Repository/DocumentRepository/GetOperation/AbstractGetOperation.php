<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\GetOperation;

use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\AbstractAttributeOperation;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;

abstract class AbstractGetOperation extends AbstractAttributeOperation
{
    protected function getDataByProjectionPath(): ?array
    {
        return $this->repositoryContext
            ->getClient()
            ->get(
                $this->primaryKey,
                $this->repositoryContext
                    ->getQueryBuilder()
                    ->getItem($this->getTableName())
                    ->itemKey($this->getItemKey())
                    ->setProjections([$this->projectionAttrPath])
                    ->setConsistentRead($this->consistentRead)
                    ->getQuery()
            )
        ;
    }
}
