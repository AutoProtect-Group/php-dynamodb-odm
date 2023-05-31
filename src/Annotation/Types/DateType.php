<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class DateType
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class DateType implements TypeInterface
{
    public const TYPE_NAME = 'datetime';
    public const DEFAULT_DATETIME_FORMAT = \DateTimeInterface::ATOM;

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
}
