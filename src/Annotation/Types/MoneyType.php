<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;

/**
 * @deprecated
 * Class MoneyType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MoneyType implements TypeInterface
{
    public const TYPE_NAME = 'money';

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
