<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactoryInterface;
use Autoprotect\DynamodbODM\Query\OperationBuilder\BatchWriteItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\DeleteItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GetItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GsiQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\PutItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\QueryQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\RemoveItemAttributeBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\ScanQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\TransactWriteItemsBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use ReflectionException;

class QueryBuilder extends AbstractQueryBuilder
{
    protected ExpressionFactoryInterface $expressionFactory;

    public function __construct(Marshaler $marshaler, ExpressionFactoryInterface $expressionFactory)
    {
        parent::__construct($marshaler);
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * @return BatchWriteItemBuilder
     */
    public function batchWriteItem(): BatchWriteItemBuilder
    {
        return new BatchWriteItemBuilder($this->marshaler);
    }

    /**
     * @return TransactWriteItemsBuilder
     */
    public function transactWriteItem(): TransactWriteItemsBuilder
    {
        return new TransactWriteItemsBuilder($this->marshaler);
    }

    /**
     * @param string $tableName
     * @param int|null $limit
     *
     * @return ScanQueryBuilder
     */
    public function scan(string $tableName, ?int $limit): ScanQueryBuilder
    {
        return new ScanQueryBuilder($this->marshaler, $tableName, $limit, $this->expressionFactory);
    }

    /**
     * @param string $tableName
     *
     * @return GetItemBuilder
     */
    public function getItem(string $tableName): GetItemBuilder
    {
        return new GetItemBuilder($this->marshaler, $tableName, $this->expressionFactory);
    }

    /**
     * @param string $tableName
     *
     * @return PutItemBuilder
     */
    public function putItem(string $tableName): PutItemBuilder
    {
        return new PutItemBuilder($this->marshaler, $tableName);
    }

    /**
     * @param string $tableName
     *
     * @return UpdateItemBuilder
     * @throws Exception\ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function updateItem(string $tableName): UpdateItemBuilder
    {
        return new UpdateItemBuilder($this->marshaler, $tableName);
    }

    /**
     * @param string $tableName
     *
     * @return RemoveItemAttributeBuilder
     * @throws Exception\ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function removeItemAttribute(string $tableName): RemoveItemAttributeBuilder
    {
        return new RemoveItemAttributeBuilder($this->marshaler, $tableName);
    }

    /**
     * @param string $tableName
     *
     * @return DeleteItemBuilder
     */
    public function deleteItem(string $tableName): DeleteItemBuilder
    {
        return new DeleteItemBuilder($this->marshaler, $tableName);
    }

    /**
     * @param string $tableName
     *
     * @throws Exception\ExpressionNotFoundException
     * @throws ReflectionException
     *
     * @return QueryQueryBuilder
     */
    public function query(string $tableName): QueryQueryBuilder
    {
        return new QueryQueryBuilder($this->marshaler, $tableName, $this->expressionFactory);
    }

    /**
     * @param string $tableName
     * @param string $indexName
     *
     * @throws Exception\ExpressionNotFoundException
     * @throws ReflectionException
     *
     * @return GsiQueryBuilder
     */
    public function queryIndex(string $tableName, string $indexName): GsiQueryBuilder
    {
        return new GsiQueryBuilder($this->marshaler, $tableName, $indexName, $this->expressionFactory);
    }
}
