<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ScalarCollection;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;

interface ScalarCollectionTypeHydratorInterface
{
    /**
     * Hydrate scalar array from dynamo DB
     *
     * @param PropertyInterface $annotationProperty
     * @param array             $scalarData
     *
     * @return array
     */
    public function hydrate(PropertyInterface $annotationProperty, array $scalarData): array;
}
