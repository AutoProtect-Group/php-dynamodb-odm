<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money;

use Money\Money;

interface MoneyTypeHydratorInterface
{
    public const INVALID_MONEY_CURRENCY_CODE = 'INVALID_MONEY_CURRENCY';
    public const INVALID_MONEY_AMOUNT = '-1';
    public function hydrate(array $moneyData): Money;
}
