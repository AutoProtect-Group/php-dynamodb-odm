<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class EqExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class ListAppendExpression extends ScalarArgExpression
{
    private const LIST_CONDITION_KEY = '#list';

    /**
     * @var string
     */
    protected string $expression = '#list = list_append(:%s, #list)';

    /**
     * @var string
     */
    protected string $operator = ' ,';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf(
            str_replace(self::LIST_CONDITION_KEY, $this->columnKey, $this->expression),
            $this->getParamValueHash()
        );
    }
}
