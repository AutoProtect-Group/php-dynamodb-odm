<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class RemoveExpressionCollection
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
class RemoveExpressionCollection extends SetExpressionCollection
{
    protected const EXPRESSION_TEMPLATE = 'REMOVE %s';
}
