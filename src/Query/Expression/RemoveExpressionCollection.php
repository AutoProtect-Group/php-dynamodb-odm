<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class RemoveExpressionCollection
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class RemoveExpressionCollection extends SetExpressionCollection
{
    protected const EXPRESSION_TEMPLATE = 'REMOVE %s';
}
