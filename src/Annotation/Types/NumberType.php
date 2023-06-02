<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class NumberType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NumberType implements TypeInterface
{
    public const TYPE_NAME = 'number';

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
