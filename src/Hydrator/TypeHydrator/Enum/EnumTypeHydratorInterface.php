<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Enum;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Model\EnumerationInterface;

interface EnumTypeHydratorInterface
{
    public function hydrate(
        string $modelClassName,
        PropertyInterface $annotationProperty,
        int|string $enum,
    ): ?EnumerationInterface;
}
