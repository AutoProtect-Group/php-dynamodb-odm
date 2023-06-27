<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\AbstractQueryBuilder;
use Autoprotect\DynamodbODM\Query\Expression\AttributeValueExpression;
use Autoprotect\DynamodbODM\Query\Expression\BeginsWithExpression;
use Autoprotect\DynamodbODM\Query\Expression\ContainsExpression;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionInterface;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\EqExpression;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactoryInterface;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

class QueryQueryBuilder extends AbstractQueryBuilder
{
    use ConsistentReadProperty;

    public const KEY_CONDITION_EXPRESSION = 'KeyConditionExpression';
    public const FILTER_CONDITION_EXPRESSION = 'FilterExpression';
    public const EXPRESSION_ATTRIBUTE_VALUES = 'ExpressionAttributeValues';
    public const TABLE_NAME = 'TableName';
    public const CONSISTENT_READ = 'ConsistentRead';
    public const SCAN_INDEX_FORWARD = 'ScanIndexForward';

    public const LIMIT = 'Limit';

    protected ExpressionInterface $keyConditionExpression;
    protected ExpressionInterface $filterConditionExpression;
    protected ExpressionInterface $expressionAttributeValues;
    protected ExpressionFactory $expressionFactory;
    protected string $tableName;

    protected ?int $limit = null;

    protected ?bool $scanIndexForward = null;

    /**
     * @param Marshaler $marshaler
     * @param string $tableName
     * @param ExpressionFactoryInterface|null $expressionFactory
     *
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     */
    public function __construct(
        Marshaler $marshaler,
        string $tableName,
        ExpressionFactoryInterface $expressionFactory = null
    ) {
        parent::__construct($marshaler);

        $this->tableName = $tableName;

        if ($expressionFactory === null) {
            $expressionFactory = new ExpressionFactory(
                $marshaler
            );
        }

        $this->expressionFactory = $expressionFactory;

        $this->keyConditionExpression = $this->expressionFactory
            ->withClassName(ExpressionCollection::class)
            ->withValue([])
            ->getExpression();

        $this->filterConditionExpression = $this->expressionFactory
            ->withClassName(ExpressionCollection::class)
            ->withValue([])
            ->getExpression();

        $this->expressionAttributeValues = $this->expressionFactory
            ->withClassName(ExpressionCollection::class)
            ->getExpression();
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param bool|null $scanIndexForward
     *
     * @return self
     */
    public function setScanIndexForward(?bool $scanIndexForward): self
    {
        $this->scanIndexForward = $scanIndexForward;
        return $this;
    }

    private function getExpressionAttributeValues(): array
    {
        return $this->expressionAttributeValues->getValue();
    }

    private function getConditionExpressionString(): string
    {
        return $this->keyConditionExpression->getExpressionString();
    }

    private function getFilterConditionExpressionString(): string
    {
        return $this->filterConditionExpression->getExpressionString();
    }

    private function getConditionExpressionFunctionType(?string $type): string
    {
        return match ($type) {
            self::KEY_CONDITION_EXPRESSION_BEGINS_WITH => BeginsWithExpression::class,
            default => EqExpression::class,
        };
    }

    public function addKeyCondition(
        string $key,
        $value,
        string $operator = self::OPERATOR_AND,
        ?string $type = null
    ): self {

        $expression = $this->expressionFactory
            ->withClassName($this->getConditionExpressionFunctionType($type))
            ->withKey(sprintf('%s.%s', $key, $value))
            ->withColumnKey($key)
            ->withValue($key)
            ->withOperator($operator)
            ->getExpression();

        $this->keyConditionExpression->addExpression($expression);

        $expression = $this->expressionFactory
            ->withClassName(AttributeValueExpression::class)
            ->withKey(sprintf('%s.%s', $key, $value))
            ->withColumnKey($key)
            ->withValue($value)
            ->getExpression();

        $this->expressionAttributeValues->addExpression($expression);

        return $this;
    }

    public function addFilterConditions(array $filterConditions): self
    {
        foreach ($filterConditions as $key => $value) {
            //If condition value provided as scalar value then build filter for this value
            if (!is_array($value)) {
                $this->addFilterCondition($key, $value);
            }

            //If condition values were not provided then stop building filter for this field
            if (!isset($value[DynamoDBRepository::KEY_EXPRESSION_VALUE_FIELD])) {
                continue;
            }

            //If for the field provided only one condition value then build single condition
            if (!is_array($filterConditionValues = $value[DynamoDBRepository::KEY_EXPRESSION_VALUE_FIELD])) {
                $this->addFilterCondition(
                    $key,
                    $filterConditionValues,
                );
                continue;
            }

            //Build filter condition for provided multiple field condition values
            array_map(function ($filterValue) use ($key) {
                $this->addFilterCondition(
                    $key,
                    $filterValue,
                    $value[DynamoDBRepository::KEY_EXPRESSION_OPERATOR_FIELD] ?? self::OPERATOR_OR
                );
            }, $filterConditionValues);
        }

        return $this;
    }

    public function addFilterCondition(
        string $key,
        $value,
        string $operator = self::OPERATOR_AND,
        ?string $type = null
    ): self {

        $expression = $this->expressionFactory
            ->withClassName($this->getConditionExpressionFunctionType($type))
            ->withKey(sprintf('%s.%s', $key, $value))
            ->withColumnKey($key)
            ->withValue($key)
            ->withOperator($operator)
            ->getExpression();

        $this->filterConditionExpression->addExpression($expression);

        $expression = $this->expressionFactory
            ->withClassName(AttributeValueExpression::class)
            ->withKey(sprintf('%s.%s', $key, $value))
            ->withColumnKey($key)
            ->withValue($value)
            ->getExpression();

        $this->expressionAttributeValues->addExpression($expression);

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $query = [
            self::TABLE_NAME => $this->tableName,
            self::KEY_CONDITION_EXPRESSION => $this->getConditionExpressionString(),
            self::EXPRESSION_ATTRIBUTE_VALUES => $this->getExpressionAttributeValues(),
            self::CONSISTENT_READ => $this->consistentRead,
        ];

        //In case filter condition expression is not empty then add it to query
        if (!empty(str_replace(array('(', ')'), '', $this->getFilterConditionExpressionString()))) {
            $query[self::FILTER_CONDITION_EXPRESSION] = $this->getFilterConditionExpressionString();
        }

        if ($this->limit !== null) {
            $query[self::LIMIT] = $this->limit;
        }

        if ($this->scanIndexForward !== null) {
            $query[self::SCAN_INDEX_FORWARD] = $this->scanIndexForward;
        }

        return $query;
    }
}
