<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\String;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;

interface StringTypeHydratorInterface
{
    public function hydrate(PropertyInterface $annotationProperty, mixed $value): string;
}
