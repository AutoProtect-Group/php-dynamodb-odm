<?php


namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

/**
 * Trait ConsistentReadProperty
 *
 * @property bool $consistentRead
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
 */
trait ConsistentReadProperty
{
    /**
     * Determines the read consistency model
     *
     * @var bool
     */
    protected bool $consistentRead = true;

    public function setConsistentRead(bool $consistentRead): self
    {
        $this->consistentRead = $consistentRead;

        return $this;
    }
}
