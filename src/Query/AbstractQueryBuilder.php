<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query;

use Aws\DynamoDb\Marshaler;

/**
 * Class QueryBuilder
 *
 * @package Autoprotect\DynamodbODM\Query
 */
abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var Marshaler
     */
    protected Marshaler $marshaler;

    /**
     * @var array
     */
    protected array $query = [];

    /**
     * AbstractQueryBuilder constructor.
     * @param Marshaler $marshaler
     */
    public function __construct(Marshaler $marshaler)
    {
        $this->marshaler = $marshaler;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }
}
