<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\OperationBuilder;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Expression\AttributeNameExpression;
use Autoprotect\DynamodbODM\Query\Expression\AttributesNamesExpressionCollection;
use Autoprotect\DynamodbODM\Query\Expression\ExpressionInterface;
use Autoprotect\DynamodbODM\Query\Expression\ProjectionExpression;
use Autoprotect\DynamodbODM\Query\Expression\ProjectionExpressionCollection;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactoryInterface;

/**
 * Class GetItemBuilder
 *
 * @package Autoprotect\DynamodbODM\Query\OperationBuilder
 */
class GetItemBuilder extends ItemOperationBuilder
{
    use ConsistentReadProperty;

    /**
     * List of DynamoDb keys for GetItem request
     */
    public const CONSISTENT_READ = 'ConsistentRead';
    public const PROJECTION_EXPRESSION = 'ProjectionExpression';
    public const EXPRESSION_ATTRIBUTES_NAMES = 'ExpressionAttributeNames';

    /**
     * A list of all projection expressions
     *
     * @var ProjectionExpressionCollection
     */
    protected ProjectionExpressionCollection $projectionExpressions;

    /**
     * A list of attribute expressions
     *
     * @var AttributesNamesExpressionCollection
     */
    protected AttributesNamesExpressionCollection $attributeNameExpressions;

    /**
     * @var ExpressionFactoryInterface
     */
    protected ExpressionFactoryInterface $expressionFactory;

    /**
     *
     * @param Marshaler                  $marshaler
     * @param string                     $tableName
     * @param ExpressionFactoryInterface $expressionFactory
     */
    public function __construct(Marshaler $marshaler, string $tableName, ExpressionFactoryInterface $expressionFactory)
    {
        parent::__construct($marshaler, $tableName);
        $this->expressionFactory = $expressionFactory;
        $this->projectionExpressions = $this->expressionFactory
            ->withClassName(ProjectionExpressionCollection::class)
            ->withOperator(ProjectionExpressionCollection::DEFAULT_OPERATOR)
            ->withValue([])
            ->getExpression();

        $this->attributeNameExpressions = $this->expressionFactory
            ->withClassName(AttributesNamesExpressionCollection::class)
            ->withOperator(AttributesNamesExpressionCollection::DEFAULT_OPERATOR)
            ->withValue([])
            ->getExpression();
    }

    /**
     * Set a list
     *
     * @param array $projectionsList
     *
     * @return $this
     */
    public function setProjections(array $projectionsList): self
    {
        $this->projectionExpressions->addExpressionArray(array_map(
            function (string $rawProjection): ExpressionInterface {
                /** @var AttributeNameExpression $attributeNameExpression */

                $attributes = explode('.', $rawProjection);

                $attributesParametrized = [];

                foreach ($attributes as $attribute) {
                    $attributeNameExpression = $this->expressionFactory
                        ->withClassName(AttributeNameExpression::class)
                        ->withKey($attribute)
                        ->withValue($attribute)
                        ->getExpression();

                    $this->attributeNameExpressions->addExpression($attributeNameExpression);

                    $attributesParametrized[] = $attributeNameExpression;
                }

                return $this->expressionFactory
                    ->withClassName(ProjectionExpression::class)
                    ->withOperator(ProjectionExpression::DEFAULT_OPERATOR)
                    ->withValue(array_reduce(
                        $attributesParametrized,
                        function (string $attr, AttributeNameExpression $ex) {
                            return (empty($attr) ? $ex->getParamName() : $attr . '.' . $ex->getParamName());
                        },
                        ''
                    ))
                    ->getExpression();
            },
            $projectionsList,
        ));

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $this->query = array_merge(
            [
                self::TABLE_NAME => $this->tableName,
                self::REQUEST_KEY => $this->key,
                self::CONSISTENT_READ => $this->consistentRead,
            ],
            ($this->projectionExpressions->isEmpty()
                ? [] : [self::PROJECTION_EXPRESSION => $this->projectionExpressions->getExpressionString()]),
            ($this->attributeNameExpressions->isEmpty()
                ? [] : [self::EXPRESSION_ATTRIBUTES_NAMES => $this->attributeNameExpressions->getValue()])
        );

        return $this->query;
    }
}
