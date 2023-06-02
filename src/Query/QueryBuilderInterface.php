<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Query;

/**
 * Interface QueryBuilderInterface
 *
 * @package Autoprotect\DynamodbODM\Query
 */
interface QueryBuilderInterface
{
    public const OPERATOR_OR = 'OR';
    public const OPERATOR_AND = 'AND';
    public const OPERATOR_NOT = 'NOT';
    public const OPERATOR_OR_NOT = 'OR NOT';

    public const KEY_CONDITION_EXPRESSION_BEGINS_WITH = 'begins_with';

    /**
     * Returns query based on builder set up
     *
     * @return array
     */
    public function getQuery(): array;
}
