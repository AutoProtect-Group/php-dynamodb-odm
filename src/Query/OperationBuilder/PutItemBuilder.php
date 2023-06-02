<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Expression\Condition\AttributeNotExistsExpression;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionInterface;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use ReflectionException;

/**
 * Class PutItemBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\Operation
 */
class PutItemBuilder extends ItemOperationBuilder
{
    public const REQUEST_ITEM = 'Item';
    public const CONDITION_EXPRESSION = 'ConditionExpression';

    protected ExpressionInterface $conditionExpression;
    protected ExpressionFactory $expressionFactory;

    /**
     * Key value array of new item data
     *
     * @var array
     */
    private array $data;

    /**
     * @param Marshaler $marshaler
     * @param string $tableName
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function __construct(Marshaler $marshaler, string $tableName)
    {
        parent::__construct($marshaler, $tableName);

        $this->expressionFactory = new ExpressionFactory($marshaler);

        $this->conditionExpression = $this->expressionFactory
            ->withClassName(ExpressionCollection::class)
            ->withValue([])
            ->getExpression();
    }

    /**
     * @param array $data
     *
     * @return PutItemBuilder
     */
    public function itemData(array $data): static
    {
        $this->data = $this->marshaler->marshalItem($data);

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return array_merge(
            [
                self::TABLE_NAME => $this->tableName,
                self::REQUEST_ITEM => $this->data,
            ],
            ($this->conditionExpression->isEmpty())
                ? [] : [self::CONDITION_EXPRESSION => $this->conditionExpression->getExpressionString()]
        );
    }

    /**
     * @param string $key
     * @param string $operator
     *
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     *
     * @return PutItemBuilder
     */
    public function addCondition(string $key, string $operator = self::OPERATOR_OR): static
    {
        $expression = $this->expressionFactory
            ->withClassName(AttributeNotExistsExpression::class)
            ->withKey($key)
            ->withColumnKey($key)
            ->withValue($key)
            ->withOperator($operator)
            ->getExpression();

        $this->conditionExpression->addExpression($expression);

        return $this;
    }
}
