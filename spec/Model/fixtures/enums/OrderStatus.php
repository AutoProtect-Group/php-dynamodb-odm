<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures\enums;

use Autoprotect\DynamodbODM\Model\EnumerationInterface;

enum OrderStatus: string implements EnumerationInterface
{
    case CREATED = 'created';
    case IN_REVIEW = 'in-review';
    case PAYMENT_COMPLETE = 'payment-complete';

    public function toScalar(): int|string
    {
        return $this->value;
    }
}
