<?php

namespace Autoprotect\DynamodbODM\Client;

use Aws\DynamoDb\DynamoDbClient as DynamoDbClientSDK;
use Aws\DynamoDb\Marshaler;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Class DynamoDBPDOClient
 *
 * @package Autoprotect\DynamodbODM\Client
 */
class DynamoDBPDOClient implements PDOClientInterface
{
    protected const KEY = 'Key';
    protected const ATTRIBUTES = 'Attributes';
    protected const ITEMS = 'Items';
    protected const ITEM = 'Item';

    protected DynamoDbClientSDK $dynamoDbClientSDK;

    /**
     * DynamoDBPDOClient constructor.
     *
     * @param DynamoDbClientSDK $dynamoDbClientSDK
     */
    public function __construct(DynamoDbClientSDK $dynamoDbClientSDK)
    {
        $this->dynamoDbClientSDK = $dynamoDbClientSDK;
    }

    /**
     * {@inheritDoc}
     */
    public function put(array $data)
    {
        return $this->dynamoDbClientSDK->putItem($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id, array $data)
    {
        return $this->dynamoDbClientSDK
            ->deleteItem($this->overWriteKeys($id, $data))->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id, array $data)
    {
        return $this->dynamoDbClientSDK
                ->getItem($this->overWriteKeys($id, $data))
                ->toArray()[self::ITEM] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data)
    {
        return $this->dynamoDbClientSDK
            ->updateItem($this->overWriteKeys($id, $data))
            ->toArray()[self::ATTRIBUTES] ?? [];
    }

    /**
     * This method overwrites key in query array by provided id
     *
     * @param string $id
     * @param array $data
     *
     * @return array
     */
    protected function overWriteKeys(string $id, array $data): array
    {
        $primaryKey = array_key_first($data[self::KEY]);
        $overwritingKey = [
            $primaryKey => (new Marshaler())->marshalValue($id),
        ];
        $data[self::KEY] = array_intersect_key(array_merge($data[self::KEY], $overwritingKey), $data[self::KEY]);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function putAsync(array $data): PromiseInterface
    {
        return $this->dynamoDbClientSDK->putItemAsync($data);
    }
}
