<?php

namespace Autoprotect\DynamodbODM\Annotation\Key;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 *
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Sort
{
}
