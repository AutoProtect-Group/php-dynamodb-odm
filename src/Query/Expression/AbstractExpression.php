<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Expression;

use Aws\DynamoDb\Marshaler;

/**
 * Class AbstractExpression
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
abstract class AbstractExpression implements ExpressionInterface
{
    /**
     * @var string
     */
    protected string $operator = '';

    /**
     * @var string
     */
    protected string $expression;

    /**
     * @var string
     */
    protected string $key;

    /**
     * @var string
     */
    protected string $columnKey;

    /**
     * @var Marshaler
     */
    protected $marshaler;

    /**
     * AbstractExpression constructor.
     * @param Marshaler $marshaler
     */
    public function __construct(Marshaler $marshaler)
    {
        $this->marshaler = $marshaler;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function setValue($value): self;

    /**
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $columnKey
     *
     * @return self
     */
    public function setColumnKey(string $columnKey): self
    {
        $this->columnKey = $columnKey;

        return $this;
    }

    public function getExpressionValues(): array
    {
        return [];
    }

    /**
     * @param string $operator
     * @return self
     */
    public function setOperator(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
}
