<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

/**
 * Class ProjectionExpression
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\Expression\
 */
class ProjectionExpression extends AbstractExpression
{

    public const DEFAULT_OPERATOR = '';

    /**
     * @var string
     */
    protected string $value;

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpressionString(): string
    {
        return $this->getValue() . $this->getOperator();
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
