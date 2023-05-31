<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class AttributeNameExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression
 */
class AttributeValueExpression extends ScalarArgExpression
{
    /**
     * {@inheritDoc}
     */
    public function getValue(): array
    {
        return [$this->getParamName() => $this->marshaler->marshalValue($this->value)];
    }

    /**
     * {@inheritDoc}
     */
    public function getParamName(): string
    {
        return ':' . $this->getKeyHash();
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeParamValue(): void
    {
        $this->paramName = '';
    }
}
