<?php

namespace Autoprotect\DynamodbODM\Annotation\Key;

use Doctrine\Common\Annotations\Annotation;
use Attribute;

/**
 * Class Primary
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation\Key
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @TODO add primary key type
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Primary
{

}
