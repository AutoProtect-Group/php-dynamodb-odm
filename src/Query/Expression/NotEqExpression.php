<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class NotEqExpression
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
class NotEqExpression extends ScalarArgExpression
{
    /**
     * @var string
     */
    protected string $expression = '%s <> :%s';
}
