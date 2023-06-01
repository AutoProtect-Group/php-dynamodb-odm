<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class BooleanType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class BooleanType implements TypeInterface
{
    public const TYPE_NAME = 'boolean';

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
