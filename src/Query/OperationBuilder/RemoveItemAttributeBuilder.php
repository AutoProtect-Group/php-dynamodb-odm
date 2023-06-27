<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Expression\AttributeNameExpression;
use Autoprotect\DynamodbODM\Query\Expression\AttributesNamesExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\RemoveExpression;
use Autoprotect\DynamodbODM\Query\Expression\RemoveExpressionCollection;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use ReflectionException;

/**
 * Class RemoveItemAttributeBuilder
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class RemoveItemAttributeBuilder extends ItemOperationBuilder
{
    /**
     * List of DynamoDb keys for UpdateItem request
     */
    public const RETURN_VALUES = 'ReturnValues';
    public const ALL_NEW = 'ALL_NEW';

    public const EXPRESSION_ATTRIBUTE_NAMES = 'ExpressionAttributeNames';
    public const UPDATE_EXPRESSION = 'UpdateExpression';

    /**
     * @var ExpressionFactory
     */
    protected ExpressionFactory $expressionFactory;

    /**
     * A list of expression attribute names
     *
     * @var AttributesNamesExpressionCollection
     */
    protected $expressionAttributeNames;

    /**
     * A list of setExpressions
     *
     * @var RemoveExpressionCollection
     */
    protected $removeExpressionCollection;

    /**
     * UpdateItemBuilder constructor.
     *
     * @param Marshaler $marshaler
     * @param string    $tableName
     *
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function __construct(Marshaler $marshaler, string $tableName)
    {
        parent::__construct($marshaler, $tableName);
        $this->expressionFactory = new ExpressionFactory($marshaler);

        $this->expressionAttributeNames = $this->expressionFactory
            ->withClassName(AttributesNamesExpressionCollection::class)
            ->withValue([])
            ->getExpression();

        $this->removeExpressionCollection = $this->expressionFactory
            ->withClassName(RemoveExpressionCollection::class)
            ->getExpression();
    }

    /**
     * @param array $attributePaths
     *
     * @return RemoveItemAttributeBuilder
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function removeAttributesByPath(array $attributePaths): RemoveItemAttributeBuilder
    {
        foreach ($attributePaths as $attributePath) {
            $projectionAttributeName = $this->addProjectionAttribute($attributePath);

            $removeAttributeExpression = $this->expressionFactory
                ->withClassName(RemoveExpression::class)
                ->withKey($projectionAttributeName)
                ->withColumnKey($projectionAttributeName)
                ->getExpression();

            $this->removeExpressionCollection->addExpression($removeAttributeExpression);
        }

        return $this;
    }

    /**
     * @param string $projectionAttributeName
     *
     * @return string
     */
    public function addProjectionAttribute(string $projectionAttributeName): string
    {
        $separatedAttributes = explode('.', $projectionAttributeName);

        $expressions = array_map(function (string $attrName): string {
            $attributeNameExpression = $this->expressionFactory
                ->withClassName(AttributeNameExpression::class)
                ->withKey($attrName)
                ->withColumnKey($attrName)
                ->withValue($attrName)
                ->getExpression();

            $this->expressionAttributeNames->addExpression($attributeNameExpression);

            return $attributeNameExpression->getParamName();
        }, $separatedAttributes);

        $projectionAttributeName = implode('.', $expressions);

        return $projectionAttributeName;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $this->query = [
            self::TABLE_NAME => $this->tableName,

            self::EXPRESSION_ATTRIBUTE_NAMES => $this->getExpressionAttributeNames(),

            self::REQUEST_KEY => $this->key,

            self::RETURN_VALUES => self::ALL_NEW,
            self::UPDATE_EXPRESSION => $this->getUpdateExpression()
        ];

        return $this->query;
    }

    /**
     * @return array
     */
    private function getExpressionAttributeNames(): array
    {
        return $this->expressionAttributeNames->getValue();
    }

    /**
     * @return string
     */
    private function getUpdateExpression(): string
    {
        return $this->removeExpressionCollection->getExpressionString();
    }
}
