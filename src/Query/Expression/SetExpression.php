<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class EqExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class SetExpression extends ScalarArgExpression
{
    /**
     * @var string
     */
    protected string $expression = '%s = :%s';

    /**
     * @var string
     */
    protected string $operator = ' ,';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->columnKey, $this->getParamValueHash());
    }
}
