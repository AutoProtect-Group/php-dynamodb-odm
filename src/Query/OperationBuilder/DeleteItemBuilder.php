<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

/**
 * Class DeleteItemBuilder
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
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
