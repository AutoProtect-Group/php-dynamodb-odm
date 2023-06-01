<?php

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Expression\AttributeNameExpression;
use Autoprotect\DynamodbODM\Query\Expression\AttributesNamesExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\AttributeValueExpression;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\ListAppendExpression;
use Autoprotect\DynamodbODM\Query\Expression\ScalarArgExpression;
use Autoprotect\DynamodbODM\Query\Expression\SetExpression;
use Autoprotect\DynamodbODM\Query\Expression\SetExpressionCollection;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use ReflectionException;

/**
 * Class UpdateItem
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class UpdateItemBuilder extends ItemOperationBuilder
{
    /**
     * List of DynamoDb keys for UpdateItem request
     */
    public const RETURN_VALUES = 'ReturnValues';
    public const ALL_NEW = 'ALL_NEW';
    public const NONE = 'NONE';
    public const UPDATED_NEW = 'UPDATED_NEW';

    public const EXPRESSION_ATTRIBUTE_NAMES = 'ExpressionAttributeNames';
    public const EXPRESSION_ATTRIBUTE_VALUES = 'ExpressionAttributeValues';
    public const UPDATE_EXPRESSION = 'UpdateExpression';
    public const CONDITION_EXPRESSION = 'ConditionExpression';

    /**
     * @var ExpressionFactory
     */
    protected ExpressionFactory $expressionFactory;

    /**
     * @var string
     */
    private string $returnValuesType = self::ALL_NEW;

    /**
     * A list of expression attribute names
     *
     * @var AttributesNamesExpressionCollection
     */
    protected AttributesNamesExpressionCollection $expressionAttributeNames;

    /**
     * A list of expressions attribute values
     *
     * @var ExpressionCollection
     */
    protected ExpressionCollection $expressionAttributeValues;

    /**
     * A list of setExpressions
     *
     * @var SetExpressionCollection
     */
    protected SetExpressionCollection $setExpressionCollection;

    protected ExpressionCollection $conditionExpression;

    /**
     * UpdateItemBuilder constructor.
     *
     * @param Marshaler $marshaler
     * @param string $tableName
     *
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

        $this->expressionAttributeNames = $this->expressionFactory
            ->withClassName(AttributesNamesExpressionCollection::class)
            ->withValue([])
            ->getExpression();

        $this->expressionAttributeValues = $this->expressionFactory
            ->withClassName(ExpressionCollection::class)
            ->getExpression();

        $this->setExpressionCollection = $this->expressionFactory
            ->withClassName(SetExpressionCollection::class)
            ->getExpression();
    }

    /**
     * @param array $attributes
     *
     * @return UpdateItemBuilder
     */
    public function attributes(array $attributes): self
    {
        $this->processAttributes($attributes, SetExpression::class);
        return $this;
    }

    public function attributesAppendList(array $attributes): self
    {
        $this->processAttributes($attributes, ListAppendExpression::class);
        return $this;
    }

    private function processAttributes(array $attributes, string $expressionType): void
    {
        foreach ($attributes as $attrName => $attrValue) {
            $projectionAttributeName = $this->addProjectionAttribute($attrName);

            $attributeValueExpression = $this->expressionFactory
                ->withClassName(AttributeValueExpression::class)
                ->withKey($projectionAttributeName)
                ->withColumnKey($projectionAttributeName)
                ->withValue($attrValue)
                ->getExpression();

            $this->expressionAttributeValues->addExpression($attributeValueExpression);

            $setValueExpression = $this->expressionFactory
                ->withClassName($expressionType)
                ->withKey($projectionAttributeName)
                ->withColumnKey($projectionAttributeName)
                ->withValue($projectionAttributeName)
                ->getExpression();

            $this->setExpressionCollection->addExpression($setValueExpression);
        }
    }

    /**
     * @param string $projectionAttributeName
     * @param string $salt
     * @return string
     *
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function addProjectionAttribute(string $projectionAttributeName, string $salt = ''): string
    {
        $separatedAttributes = explode('.', $projectionAttributeName);
        $expressions = [];

        foreach ($separatedAttributes as $attrName) {
            $attrNameKey = implode('.', array_filter([$attrName, $salt]));
            $attributeNameExpression = $this->expressionFactory
                ->withClassName(AttributeNameExpression::class)
                ->withKey($attrNameKey)
                ->withColumnKey($attrNameKey)
                ->withValue($attrName)
                ->getExpression();

            $this->expressionAttributeNames->addExpression($attributeNameExpression);
            $expressions[] = $attributeNameExpression->getParamName();
        }

        $projectionAttributeName = implode(
            '.',
            array_map(static function ($expression) {
                return $expression;
            }, $expressions)
        );

        return $projectionAttributeName;
    }

    /**
     * @param string $key
     * @param string $expressionClass
     * @param string $operator
     *
     * @return UpdateItemBuilder
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function addKeyCondition(string $key, string $expressionClass, string $operator = self::OPERATOR_OR): self
    {
        $projectionAttributeName = $this->addProjectionAttribute($key);

        $expression = $this->expressionFactory
            ->withClassName($expressionClass)
            ->withKey($projectionAttributeName)
            ->withColumnKey($projectionAttributeName)
            ->withValue($projectionAttributeName)
            ->withOperator($operator)
            ->getExpression();

        $this->conditionExpression->addExpression($expression);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $expressionClass
     * @param string $operator
     *
     * @return UpdateItemBuilder
     * @throws ReflectionException
     * @throws ExpressionNotFoundException
     */
    public function addKeyValueCondition(
        string $key,
        mixed $value,
        string $expressionClass,
        string $operator = self::OPERATOR_OR
    ): self {
        // add more uniqueness here because in case when the same key (attribute) is used for updates and conditions
        // values for them are being overridden when the key is the same
        $projectionAttributeName = $this->addProjectionAttribute(
            $key,
            ScalarArgExpression::getSha256Hash($expressionClass)
        );

        $expression = $this->expressionFactory
            ->withClassName($expressionClass)
            ->withKey($projectionAttributeName)
            ->withColumnKey($projectionAttributeName)
            ->withValue($value)
            ->withOperator($operator)
            ->getExpression();

        $this->conditionExpression->addExpression($expression);

        foreach ($expression->getExpressionValues() as $valueKey => $valueData) {
            $attributeValueExpression = $this->expressionFactory
                ->withClassName(AttributeValueExpression::class)
                ->withKey($valueKey)
                ->withColumnKey($valueKey)
                ->withValue($valueData)
                ->getExpression();

            $this->expressionAttributeValues->addExpression($attributeValueExpression);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $this->query = [
            self::TABLE_NAME => $this->tableName,

            self::EXPRESSION_ATTRIBUTE_NAMES => $this->getExpressionAttributeNames(),
            self::EXPRESSION_ATTRIBUTE_VALUES => $this->getExpressionAttributeValues(),

            self::REQUEST_KEY => $this->key,

            self::RETURN_VALUES => $this->returnValuesType,
            self::UPDATE_EXPRESSION => $this->getUpdateExpression()
        ];

        if (!$this->conditionExpression->isEmpty()) {
            $this->query[self::CONDITION_EXPRESSION] = $this->conditionExpression->getExpressionString();
        }

        return $this->query;
    }

    public function withReturnNone(): static
    {
        $this->returnValuesType = self::NONE;
        return $this;
    }

    /**
     * @return array
     */
    private function getExpressionAttributeNames(): array
    {
        return $this->expressionAttributeNames->getValue();
    }

    /**
     * @return array
     */
    private function getExpressionAttributeValues(): array
    {
        return $this->expressionAttributeValues->getValue();
    }

    /**
     * @return string
     */
    private function getUpdateExpression(): string
    {
        return $this->setExpressionCollection->getExpressionString();
    }
}
