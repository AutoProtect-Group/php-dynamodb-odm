<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Autoprotect\DynamodbODM\Query\AbstractQueryBuilder;
use Aws\DynamoDb\Marshaler;

/**
 * Class CommonItemOperationBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class ItemOperationBuilder extends AbstractQueryBuilder
{
    public const TABLE_NAME  = 'TableName';
    public const REQUEST_KEY = 'Key';

    /**
     * @var string
     */
    protected string $tableName;

    /**
     * @var array
     */
    protected array $key;

    /**
     * UpdateItem constructor.
     * @param Marshaler $marshaler
     * @param string    $tableName
     */
    public function __construct(Marshaler $marshaler, string $tableName)
    {
        parent::__construct($marshaler);
        $this->tableName = $tableName;
    }

    /**
     * @param array $key
     *
     * @return $this
     */
    public function itemKey(array $key): self
    {
        $this->key = $this->marshaler->marshalItem($key);

        return $this;
    }
}
