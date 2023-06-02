<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository;

use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\CreateDocument;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\CreateDocumentCollection;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\GetDocument;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\GetDocumentCollection;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\RemoveDocument;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\UpdateDocument;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\UpdateDocumentCollection;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\ScalarOperation\GetProperty;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

/**
 * Class DocumentRepository
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository
 */
class DocumentRepository extends DynamoDBRepository
{
    public function getDocument(): GetDocument
    {
        return new GetDocument($this);
    }

    public function getDocumentProperty(): GetProperty
    {
        return new GetProperty($this);
    }

    public function createDocument(): CreateDocument
    {
        return new CreateDocument($this);
    }

    public function updateDocument(): UpdateDocument
    {
        return new UpdateDocument($this);
    }

    public function removeDocument(): RemoveDocument
    {
        return new RemoveDocument($this);
    }

    public function getDocumentCollection(): GetDocumentCollection
    {
        return new GetDocumentCollection($this);
    }

    public function updateDocumentCollection(): UpdateDocumentCollection
    {
        return new UpdateDocumentCollection($this);
    }

    public function createDocumentCollection(): CreateDocumentCollection
    {
        return new CreateDocumentCollection($this);
    }
}
