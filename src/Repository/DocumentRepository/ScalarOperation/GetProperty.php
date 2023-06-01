<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\ScalarOperation;

use Autoprotect\DynamodbODM\Repository\DocumentRepository\GetOperation\AbstractGetOperation;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;
use Autoprotect\DynamodbODM\Repository\Exception\PropertyNotFoundException;

class GetProperty extends AbstractGetOperation
{
    public function execute(): null|float|int|string|array|bool
    {
        $item = $this->getDataByProjectionPath();

        if (is_null($item)) {
            throw new EntityNotFoundException();
        }

        return $this->processResponseData($item);
    }

    protected function processResponseData(array $data): null|float|int|string|array|bool
    {
        if (empty($data)) {
            throw new PropertyNotFoundException();
        }

        return dot($this->repositoryContext->getMarshaler()->unmarshalItem($data))
            ->get($this->projectionAttrPath);
    }
}
