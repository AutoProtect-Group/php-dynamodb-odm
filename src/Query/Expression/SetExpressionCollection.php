<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class SetExpressionCollection
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
class SetExpressionCollection extends ExpressionCollection
{
    protected const GLUE_OPERATOR = ', ';
    protected const EXPRESSION_TEMPLATE = 'SET %s';

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf(
            static::EXPRESSION_TEMPLATE,
            implode(
                static::GLUE_OPERATOR,
                array_map(static function (ExpressionInterface $expression) {
                    return $expression->getExpressionString();
                }, $this->expressions)
            )
        );
    }
}
