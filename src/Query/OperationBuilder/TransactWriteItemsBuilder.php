<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Autoprotect\DynamodbODM\Query\AbstractQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\Transact\TransactQueryInterface;

/**
 * Class TransactWriteItemsBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class TransactWriteItemsBuilder extends AbstractQueryBuilder
{
    public const TRANSACT_ITEMS = 'TransactItems';

    /**
     * @var array
     */
    protected array $query = [
        self::TRANSACT_ITEMS => []
    ];

    /**
     * @param TransactQueryInterface $transactQuery
     *
     * @return $this
     */
    public function addTransaction(TransactQueryInterface $transactQuery): self
    {
        $this->query[self::TRANSACT_ITEMS][] = [$transactQuery->getQueryType() => $transactQuery->getQuery()];

        return $this;
    }
}
