<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;

/**
 * Class ScalarCollectionType
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ScalarCollectionType implements TypeInterface
{
    public const TYPE_NAME = 'scalarCollection';

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return self::TYPE_NAME;
    }
}
