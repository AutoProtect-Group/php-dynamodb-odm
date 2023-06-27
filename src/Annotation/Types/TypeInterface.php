<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Interface TypeInterface
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
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
