<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Class CreateDocument
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
class CreateDocument extends AbstractAttributeOperation
{
    /**
     * @var ModelInterface
     */
    private ?ModelInterface $model;

    /**
     * @param ModelInterface $model
     *
     * @return self
     */
    public function withModel(ModelInterface $model): self
    {
        $this->model = $model;
    }

    /**
     * @return ModelInterface
     */
    public function execute(): ModelInterface
    {
        // TODO: Implement execute() method.
    }
}
