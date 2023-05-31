<?php

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;

/**
 * Class HashMapType
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @Attributes({
 *   @Attribute("modelClassName", type = "string"),
 * })
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HashMapType extends ModelType
{
    public const TYPE_NAME = 'hashMap';

    /**
     * CollectionMapType constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}
