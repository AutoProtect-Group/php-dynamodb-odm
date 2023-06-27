<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

/**
 * Class DeleteItemBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class DeleteItemBuilder extends ItemOperationBuilder
{
    /**
     * @return array
     */
    public function getQuery(): array
    {
        $this->query = [
            self::TABLE_NAME => $this->tableName,
            self::REQUEST_KEY => $this->key,
        ];

        return $this->query;
    }
}
