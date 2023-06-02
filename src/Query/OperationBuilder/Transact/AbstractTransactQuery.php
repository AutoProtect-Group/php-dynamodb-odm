<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder\Transact;

use InvalidArgumentException;

/**
 * Class AbstractTransactQuery
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder\Params
 */
abstract class AbstractTransactQuery implements TransactQueryInterface
{
    protected array $itemKey;
    protected string $tableName;
    protected string $queryType;
    protected array $query;

    /**
     * AbstractTransactQuery constructor.
     *
     * @param array $itemKey
     * @param string $tableName
     */
    public function __construct(array $itemKey, string $tableName)
    {
        $queryType = static::QUERY_TYPE;
        if (!in_array($queryType, self::ALLOWED_QUERY_TYPES)) {
            throw new InvalidArgumentException("$queryType is invalid query type");
        }
        $this->queryType = $queryType;
        $this->itemKey = $itemKey;
        $this->tableName = $tableName;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function getQuery(): array;

    /**
     * {@inheritDoc}
     */
    public function getQueryType(): string
    {
        return $this->queryType;
    }
}
