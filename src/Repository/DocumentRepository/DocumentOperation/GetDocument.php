<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\GetOperation\AbstractGetOperation;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;
use Exception;

/**
 * Class GetDocumentAttribute
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class GetDocument extends AbstractGetOperation
{
    /**
     * @throws Exception
     */
    public function execute(): ?ModelInterface
    {
        $item = $this->getDataByProjectionPath();

        if (is_null($item)) {
            return null;
        }

        $dotOutput = $this->processResponseData($item);

        return $this->repositoryContext
            ->getHydrator()
            ->hydrate($dotOutput);
    }

    /**
     * @throws EntityNotFoundException
     */
    protected function processResponseData(array $data): array
    {
        $dotOutput = dot($this->repositoryContext->getMarshaler()->unmarshalItem($data))
            ->get($this->projectionAttrPath);

        if (!$dotOutput) {
            throw new EntityNotFoundException();
        }

        return $dotOutput;
    }
}
