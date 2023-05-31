<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Interface ModelTypeInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 */
interface ModelTypeInterface
{
    /**
     * Get name of model
     *
     * @return string
     */
    public function getModelClassName(): string;
}
