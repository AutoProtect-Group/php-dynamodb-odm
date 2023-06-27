<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query\Factory;

use Autoprotect\DynamodbODM\Query\Expression\ExpressionInterface;

/**
 * Interface ExpressionFactoryInterface
 *
 * @package Autoprotect\DynamodbODM\Query\Factory
 */
interface ExpressionFactoryInterface
{
    public function withClassName(string $className): ExpressionFactoryInterface;

    public function withKey(string $key): ExpressionFactoryInterface;

    public function withValue($value): ExpressionFactoryInterface;

    public function withOperator(string $operator): ExpressionFactoryInterface;

    public function getExpression(): ExpressionInterface;
}
