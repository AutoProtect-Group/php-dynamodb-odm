<?php

declare(strict_types=1);

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException as AWSDynamoDBException;
use Autoprotect\DynamodbODM\Exception\DynamoDbAdapterException;

trait TableHelperTrait
{
    protected DynamoDbClient $dynamoDbClient;
    protected function createTableIfNotExists(string $tableName, array $parameters): void
    {
        try {
            $this->dynamoDbClient->createTable(array_merge(["TableName" => $tableName], $parameters));
            $this->dynamoDbClient->waitUntil("TableExists", ['TableName' => $tableName]);
        } catch (AWSDynamoDBException $awsDynamoDbException) {
            if ($awsDynamoDbException->getAwsErrorCode()
                === DynamoDbAdapterException::DYNAMODB_EXCEPTION_RESOURCE_IN_USE) {
                printf("Table %s already exists. Skipping new table creation", $tableName);
            }
        }
    }
}
