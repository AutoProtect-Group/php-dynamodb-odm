<?php

namespace Autoprotect\DynamodbODM\Model;

/**
 * Interface ModelInterface
 *
 * @package Autoprotect\DynamodbODM\Model
 */
interface ModelInterface
{
    /**
     * Get model table name depends on environment
     *
     * @return string
     */
    public static function getTableName(): string;
}
