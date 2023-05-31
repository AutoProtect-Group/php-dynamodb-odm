<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class AttributesNamesExpressionCollection
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class AttributesNamesExpressionCollection extends ExpressionCollection
{
    protected const GLUE_OPERATOR = '.';
    protected const EXPRESSION_TEMPLATE = '(%s)';

    /**
     * Get parameters list
     *
     * @return array[string]
     */
    public function getParameters(): array
    {
        return array_map(
            function (AttributeNameExpression $expression): string {
                return $expression->getParamName();
            },
            $this->expressions
        );
    }
}
