<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money;

use Money\Currency;
use Money\Money;
use Throwable;

class MoneyTypeHydrator implements MoneyTypeHydratorInterface
{
    protected const MONEY_TYPE_AMOUNT = 'amount';
    protected const MONEY_TYPE_CURRENCY = 'currency';

    public function hydrate(array $moneyData): Money
    {
        try {
            return new Money(
                $moneyData[ static::MONEY_TYPE_AMOUNT ],
                new Currency($moneyData[static::MONEY_TYPE_CURRENCY])
            );
        } catch (Throwable) {
            return new Money(
                static::INVALID_MONEY_AMOUNT,
                new Currency(static::INVALID_MONEY_CURRENCY_CODE)
            );
        }
    }
}
