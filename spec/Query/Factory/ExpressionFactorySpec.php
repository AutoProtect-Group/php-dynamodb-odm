<?php

namespace spec\Autoprotect\DynamodbODM\Query\Factory;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\Exception\QueryBuilderException;
use Autoprotect\DynamodbODM\Query\Expression\EqExpression;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use PhpSpec\ObjectBehavior;

/**
 * Class ExpressionFactorySpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Query\Factory
 */
class ExpressionFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(new Marshaler());
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExpressionFactory::class);
    }

    public function it_cat_build_expression_by_class_name(): void
    {
        $expressionSetUp = [
            'expression' => EqExpression::class,
            'key' => 'someKey',
            'operator' => 'and',
            'value' => 'some value',
        ];

        $this
            ->withClassName($expressionSetUp['expression'])
            ->withKey($expressionSetUp['key'])
            ->withValue($expressionSetUp['value'])
            ->withOperator($expressionSetUp['operator'])
            ->getExpression()
            ->shouldBeAnInstanceOf($expressionSetUp['expression']);
    }

    public function it_can_throw_exception_for_nonexistent_expression(): void
    {
        $invalidExpressionSetUp = [
            'expression' => 'NonExistClassName',
            'key' => 'someKey',
            'operator' => 'and',
            'value' => 'some value',
        ];

        $this
            ->withClassName($invalidExpressionSetUp['expression'])
            ->withKey($invalidExpressionSetUp['key'])
            ->withValue($invalidExpressionSetUp['value'])
            ->withOperator($invalidExpressionSetUp['operator'])
            ->shouldThrow(QueryBuilderException::class)
            ->during('getExpression');
    }
}
