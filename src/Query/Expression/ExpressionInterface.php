<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Interface ExpressionInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
interface ExpressionInterface
{
    /**
     * @return string
     */
    public function getExpressionString(): string;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getOperator(): string;
}
