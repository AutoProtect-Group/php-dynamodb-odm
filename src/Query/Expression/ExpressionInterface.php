<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Interface ExpressionInterface
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
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
