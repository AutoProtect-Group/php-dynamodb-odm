<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Expression\Condition;

use Autoprotect\DynamodbODM\Query\Expression\AttributeValueExpression;

class AttributeTypeIs extends AttributeValueExpression
{
    protected string $expression = 'attribute_type(%s, :%s)';

    public function getExpressionString(): string
    {
        return sprintf($this->expression, $this->key, $this->getKeyHash());
    }

    public function getExpressionValues(): array
    {
        return [
            $this->key => $this->getInitialValue()
        ];
    }
}
