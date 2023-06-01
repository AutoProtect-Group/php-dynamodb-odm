<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class AttributeNameExpression
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
class AttributeNameExpression extends ScalarArgExpression
{
    /**
     * {@inheritDoc}
     */
    public function getValue(): array
    {
        return [$this->getParamName() => $this->value];
    }

    /**
     * {@inheritDoc}
     */
    public function getParamName(): string
    {
        return '#' . $this->getKeyHash();
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeParamValue(): void
    {
        $this->paramName = '';
    }
}
