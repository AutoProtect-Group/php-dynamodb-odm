<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class EqExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class EqExpression extends ScalarArgExpression
{
    /**
     * @var string
     */
    protected string $expression = '%s = :%s';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->columnKey, $this->getKeyHash());
    }
}
