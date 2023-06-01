<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class StringType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StringType implements TypeInterface
{
    public const TYPE_NAME = 'string';

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
