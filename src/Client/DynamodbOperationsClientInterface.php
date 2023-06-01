<?php

namespace Autoprotect\DynamodbODM\Client;

interface DynamodbOperationsClientInterface
{
    /**
     * Batch write item
     *
     * @param array $queryParams
     *
     * @return array
     */
    public function batchWriteItem(array $queryParams): array;

    /**
     * Transact write items
     *
     * @param array $queryParams
     *
     * @return array
     */
    public function transactWriteItems(array $queryParams): array;

    /**
     * @param array $queryParams
     *
     * @return array
     */
    public function query(array $queryParams): array;

    /**
     * Scan dynamo db table with specific params
     *
     * @param array $queryParams
     *
     * @return array
     */
    public function scan(array $queryParams): array;
}
