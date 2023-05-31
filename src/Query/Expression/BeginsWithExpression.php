<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class BeginsWithExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class BeginsWithExpression extends ScalarArgExpression
{
    /**
     * @var string
     */
    protected string $expression = 'begins_with(%s, :%s)';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->columnKey, $this->getKeyHash());
    }
}
