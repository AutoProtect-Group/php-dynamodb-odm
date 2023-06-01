<?php

namespace Autoprotect\DynamodbODM\Query\Factory;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Expression\AbstractExpression as AbstractExpressionAlias;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionInterface;

/**
 * Class ExpressionFactory
 *
 * @package Autoprotect\DynamodbODM\Query\Factory
 */
class ExpressionFactory implements ExpressionFactoryInterface
{
    protected Marshaler $marshaler;

    protected ?string $className;

    protected string $key = '';
    protected string $columnKey = '';

    protected $value = null;

    protected string $operator = '';

    /**
     * Factory constructor.
     *
     * @param Marshaler $marshaler
     */
    public function __construct(Marshaler $marshaler)
    {
        $this->marshaler = $marshaler;
    }

    /**
     * @param string $className
     *
     * @return ExpressionFactory
     */
    public function withClassName(string $className): ExpressionFactoryInterface
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return ExpressionFactory
     */
    public function withKey(string $key): ExpressionFactoryInterface
    {
        $this->key = trim($key);

        return $this;
    }

    /**
     * @param string $columnKey
     *
     * @return ExpressionFactory
     */
    public function withColumnKey(string $columnKey): ExpressionFactoryInterface
    {
        $this->columnKey = trim($columnKey);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return ExpressionFactory
     */
    public function withValue($value): ExpressionFactoryInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string $operator
     *
     * @return ExpressionFactory
     */
    public function withOperator(string $operator): ExpressionFactoryInterface
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Return an instance of the given expression class name and params for it.
     *
     * @return ExpressionInterface|string
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     */
    public function getExpression(): ExpressionInterface
    {
        if (!class_exists($this->className)) {
            throw new ExpressionNotFoundException($this->className);
        }

        $reflection = new \ReflectionClass($this->className);

        $expression = ($reflection->isSubclassOf(AbstractExpressionAlias::class))
            ? $reflection->newInstance($this->marshaler)
            : $reflection->newInstance();

        /** @var AbstractExpressionAlias $expression */
        $expression
            ->setKey($this->key)
            ->setValue($this->value)
            ->setOperator($this->operator)
            ->setColumnKey($this->columnKey);

        return $expression;
    }
}
