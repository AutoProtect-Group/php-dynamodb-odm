<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Interface ModelTypeInterface
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
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
