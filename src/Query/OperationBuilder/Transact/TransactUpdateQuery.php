<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder\Transact;

use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use ReflectionException;

/**
 * Class TransactUpdateQuery
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder\Params
 */
class TransactUpdateQuery extends AbstractTransactQuery
{
    protected const QUERY_TYPE = 'Update';

    protected array $requestData;
    private UpdateItemBuilder $updateItemBuilder;

    /**
     * TransactUpdateQuery constructor.
     *
     * @param string $tableName
     * @param array $itemKey
     * @param array $requestData
     * @param UpdateItemBuilder $updateItemBuilder
     */
    public function __construct(
        string $tableName,
        array $itemKey,
        array $requestData,
        UpdateItemBuilder $updateItemBuilder
    ) {
        $this->requestData = $requestData;
        $this->updateItemBuilder = $updateItemBuilder;
        parent::__construct($itemKey, $tableName);
    }

    /**
     * {@inheritDoc}
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function getQuery(): array
    {
        return $this->updateItemBuilder
            ->attributes($this->requestData)
            ->itemKey($this->itemKey)
            ->getQuery();
    }
}
