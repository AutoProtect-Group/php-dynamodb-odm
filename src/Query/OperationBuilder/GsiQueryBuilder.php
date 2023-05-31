<?php declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactoryInterface;

/**
 * Class GsiQueryBuilder
 *
 * Builds query using global secondary index (GSI) for data retrieval
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
 */
class GsiQueryBuilder extends QueryQueryBuilder
{
    public const INDEX_NAME  = 'IndexName';

    protected string $indexName;

    public function __construct(
        Marshaler $marshaler,
        string $tableName,
        string $indexName,
        ExpressionFactoryInterface $expressionFactory = null
    ) {
        $this->indexName = $indexName;

        parent::__construct($marshaler, $tableName, $expressionFactory);
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $query = parent::getQuery();

        unset($query[self::CONSISTENT_READ]);

        $query[self::INDEX_NAME] = $this->indexName;

        return $query;
    }
}
