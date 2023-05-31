<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ScalarCollection;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;

class ScalarCollectionTypeHydrator implements ScalarCollectionTypeHydratorInterface
{
    public function __construct(
        protected readonly ?EncryptorInterface $encryptor = null,
    ) {
    }

    public function hydrate(PropertyInterface $annotationProperty, array $scalarData): array
    {
        return $annotationProperty->isEncrypted()
            ? $this->encryptor->decrypt($scalarData, $annotationProperty->getEncryptionOptions())
            : $scalarData;
    }
}
