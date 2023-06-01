<?php

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

/**
 * Interface AttributeOperationInterface
 *
 * @package Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation
 */
interface AttributeOperationInterface
{
    public function execute(): mixed;
}
