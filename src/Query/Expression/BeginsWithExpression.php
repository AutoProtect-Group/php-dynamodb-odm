<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class BeginsWithExpression
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
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
