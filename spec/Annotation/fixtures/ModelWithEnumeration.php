<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Key\Primary;
use Autoprotect\DynamodbODM\Annotation\Types\EnumType;
use Autoprotect\DynamodbODM\Annotation\Types\StringType;
use Autoprotect\DynamodbODM\Model\Model;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\enums\BackedEnumeration;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\enums\UnitEnumeration;

class ModelWithEnumeration extends Model
{
    #[Primary, StringType]
    protected string $id;

    #[EnumType]
    protected BackedEnumeration $backedEnumerationStrict;

    #[EnumType(isStrict:false)]
    protected BackedEnumeration $backedEnumerationNotStrict;

    #[EnumType]
    protected UnitEnumeration $unitEnumeration;
}
