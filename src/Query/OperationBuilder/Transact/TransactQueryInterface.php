<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder\Transact;

/**
 * Interface TransactUpdateQuery
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder\Params
 */
interface TransactQueryInterface
{
    public const ALLOWED_QUERY_TYPES = [
        'Update',
        'Delete',
        'Put',
    ];

    /**
     * @return string
     */
    public function getQueryType(): string;

    /**
     * @return array
     */
    public function getQuery(): array;
}
