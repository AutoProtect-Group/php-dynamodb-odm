<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class RemoveExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class RemoveExpression extends SetExpression
{
    protected string $expression = '%s';

    protected string $operator = ' ,';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->columnKey);
    }
}
