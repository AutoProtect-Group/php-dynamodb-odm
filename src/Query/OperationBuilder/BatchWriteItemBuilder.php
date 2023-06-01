<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Autoprotect\DynamodbODM\Query\AbstractQueryBuilder;

/**
 * Class BatchWriteItemBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class BatchWriteItemBuilder extends AbstractQueryBuilder
{
    /**
     * List of DynamoDb keys for BatchWriteItemBuilder request
     */
    public const REQUEST_KEY    = 'Key';
    public const REQUEST_ITEM   = 'Item';
    public const REQUEST_ITEMS  = 'RequestItems';
    public const DELETE_REQUEST = 'DeleteRequest';
    public const PUT_REQUEST    = 'PutRequest';

    /**
     * @var array
     */
    protected array $query = [
        self::REQUEST_ITEMS => []
    ];

    /**
     * It adds a new request for the given table
     * to DynamoDB be able to apply array of request for each table separately.
     *
     * @param string $tableName
     * @param array  $requestData
     */
    private function addRequest(string $tableName, array $requestData): void
    {
        if (empty($this->query[self::REQUEST_ITEMS][$tableName])) {
            $this->query[self::REQUEST_ITEMS][$tableName] = [$requestData];
        } else {
            $this->query[self::REQUEST_ITEMS][$tableName][] = $requestData;
        }
    }

    /**
     * @param string $tableName
     * @param array  $key
     *
     * @return $this
     */
    public function delete(string $tableName, array $key): self
    {
        $deleteRequest = [
            self::DELETE_REQUEST => [
                self::REQUEST_KEY => $this->marshaler->marshalItem($key)
            ]
        ];

        $this->addRequest($tableName, $deleteRequest);

        return $this;
    }

    /**
     * @param string $tableName
     * @param array  $item
     *
     * @return $this
     */
    public function put(string $tableName, array $item): self
    {
        $putRequest = [
            self::PUT_REQUEST => [
                self::REQUEST_ITEM => $this->marshaler->marshalItem($item)
            ]
        ];

        $this->addRequest($tableName, $putRequest);

        return $this;
    }
}
