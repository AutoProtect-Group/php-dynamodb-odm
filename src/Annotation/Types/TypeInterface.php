<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Interface TypeInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 */
interface TypeInterface
{
    /**
     * Get type name
     *
     * @return string
     */
    public function getType(): string;
}
