<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class FloatType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class FloatType extends NumberType
{
    public const TYPE_NAME = 'float';
}
