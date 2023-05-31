<?php


namespace Autoprotect\DynamodbODM\Query\Expression;

use Closure;

/**
 * Class ProjectionExpressionCollection
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class ProjectionExpressionCollection extends ExpressionCollection
{
    protected const EXPRESSION_TEMPLATE = '%s';
    protected const GLUE_OPERATOR = ', ';

    /**
     * Reducer callback function
     *
     * @return Closure
     */
    protected function getExpressionsReducerCallback(): Closure
    {
        return static function (array $expressions, ExpressionInterface $expression): array {
            $expressions[] = $expression->getExpressionString();
            return $expressions;
        };
    }
}
