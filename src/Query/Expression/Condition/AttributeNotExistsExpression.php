<?php

namespace Autoprotect\DynamodbODM\Query\Expression\Condition;

use Autoprotect\DynamodbODM\Query\Expression\ScalarArgExpression;

class AttributeNotExistsExpression extends ScalarArgExpression
{
    protected string $expression = 'attribute_not_exists(%s)';

    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->key);
    }
}
