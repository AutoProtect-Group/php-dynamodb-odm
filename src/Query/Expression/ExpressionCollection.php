<?php

namespace Autoprotect\DynamodbODM\Query\Expression;

use Closure;

/***
 * Class ExpressionCollection
 *
 * @package Autoprotect\DynamodbODM\Query\Expression
 */
class ExpressionCollection implements ExpressionInterface
{
    protected const GLUE_OPERATOR = ' ';
    public const DEFAULT_OPERATOR = 'and';
    protected const EXPRESSION_TEMPLATE = '(%s)';

    /**
     * @var array
     */
    protected array $expressions = [];

    /**
     * @var string
     */
    protected string $operator;

    /**
     * @var string
     */
    protected string $key;

    /**
     * @var string
     */
    protected string $columnKey;

    /**
     * @param array $value
     *
     * @return ExpressionCollection
     */
    public function setValue(array $value): self
    {
        $this->addExpressionArray($value);

        return $this;
    }

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
     * @param ExpressionInterface $expression
     *
     * @return self
     */
    public function addExpression(ExpressionInterface $expression): self
    {
        $this->expressions[] = $expression;

        return $this;
    }

    /**
     * @param array $expressions
     *
     * @return self
     */
    public function addExpressionArray(array $expressions): self
    {
        foreach ($expressions as $expression) {
            $this->addExpression($expression);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getExpressionString(): string
    {
        return sprintf(
            static::EXPRESSION_TEMPLATE,
            implode(
                static::GLUE_OPERATOR,
                array_reduce(
                    $this->expressions,
                    $this->getExpressionsReducerCallback(),
                    []
                )
            )
        );
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return array_reduce(
            $this->expressions,
            static function (array $value, ExpressionInterface $expression) {
                $value += $expression->getValue();
                return $value;
            },
            []
        );
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     *
     * @return self
     */
    public function setOperator($operator = self::DEFAULT_OPERATOR): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Check if collection has expressions
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->expressions);
    }

    /**
     * Reducer callback function
     *
     * @return Closure
     */
    protected function getExpressionsReducerCallback(): Closure
    {
        return static function (array $expressions, ExpressionInterface $expression): array {
            if (!empty($expressions)) {
                $expressions[] = $expression->getOperator();
            }

            $expressions[] = $expression->getExpressionString();

            return $expressions;
        };
    }

    /**
     * @param bool $columnKey
     *
     * @return self
     */
    public function setColumnKey(string $columnKey): self
    {
        $this->columnKey = $columnKey;

        return $this;
    }
}
