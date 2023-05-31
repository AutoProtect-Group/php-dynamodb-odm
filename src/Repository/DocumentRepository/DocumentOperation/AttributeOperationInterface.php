<?php

namespace Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation;

/**
 * Interface AttributeOperationInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Repository\DocumentRepository\DocumentOperation
 */
interface AttributeOperationInterface
{
    public function execute(): mixed;
}
