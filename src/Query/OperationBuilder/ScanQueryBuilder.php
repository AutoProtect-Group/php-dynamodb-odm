<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\AbstractQueryBuilder;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\EqExpression;
use Autoprotect\DynamodbODM\Query\Expression\NotEqExpression;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactoryInterface;

/**
 * Class ScanQueryBuilder
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Query\OperationBuilder
 */
class ScanQueryBuilder extends AbstractQueryBuilder
{
    use ConsistentReadProperty;

    public const CONSISTENT_READ = 'ConsistentRead';

    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';

    protected array $expressionAttributeNames = [];

    protected string $tableName;

    protected ?int $limit;

    protected ExpressionCollection $expressions;

    protected ExpressionFactory $expressionFactory;

    /**
     * Scan constructor.
     *
     * @param Marshaler $marshaler
     * @param string $tableName
     * @param int|null $limit
     * @param ExpressionFactoryInterface|null $expressionFactory
     */
    public function __construct(
        Marshaler $marshaler,
        string $tableName,
        ?int $limit,
        ExpressionFactoryInterface $expressionFactory = null
    ) {
        parent::__construct($marshaler);
        $this->tableName = $tableName;
        $this->limit = $limit;
        $this->expressions = new ExpressionCollection();

        if ($expressionFactory === null) {
            $expressionFactory = new ExpressionFactory(
                $marshaler
            );
        }

        $this->expressionFactory = $expressionFactory;
    }

    /**
     * @return ExpressionCollection
     */
    public function getExpressions(): ExpressionCollection
    {
        return $this->expressions;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function withAttributeNames(array $attributes): self
    {
        $this->expressionAttributeNames = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return self
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     */
    public function eq(string $key, $value, string $operator = self::OPERATOR_AND): self
    {
        $getExpression = $this->expressionFactory
            ->withClassName(EqExpression::class)
            ->withKey($key)
            ->withColumnKey($key)
            ->withValue($value)
            ->withOperator($operator)
            ->getExpression();

        $this->expressions->addExpression($getExpression);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return self
     * @throws ExpressionNotFoundException
     * @throws \ReflectionException
     */
    public function notEq(string $key, $value, string $operator = self::OPERATOR_AND): self
    {
        $getExpression = $this->expressionFactory
            ->withClassName(NotEqExpression::class)
            ->withKey($key)
            ->withColumnKey($key)
            ->withValue($value)
            ->withOperator($operator)
            ->getExpression();

        $this->expressions->addExpression($getExpression);

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $query = [
            'TableName' => $this->tableName,
            self::CONSISTENT_READ => $this->consistentRead
        ];

        /** AwsSDK will throw exception if limit === 0 **/
        if (!empty($this->limit)) {
            $query += [
                'Limit' => $this->limit,
            ];
        }

        $expressionAttributeValues = $this->expressions->getValue();

        if (count($expressionAttributeValues)) {
            $query += [
                'FilterExpression' => $this->expressions->getExpressionString(),
                'ExpressionAttributeValues' => $expressionAttributeValues
            ];
        }

        if (count($this->expressionAttributeNames)) {
            $query += [
                'ExpressionAttributeNames' => $this->expressionAttributeNames
            ];
        }

        return $query;
    }
}
