<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;

/**
 * Class Money
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Money implements TypeInterface
{
    public const TYPE_NAME = 'moneyObject';

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
