<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\String;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;

class StringTypeHydrator implements StringTypeHydratorInterface
{
    public function __construct(
        protected ?EncryptorInterface $encryptor,
    ) {
    }

    public function hydrate(PropertyInterface $annotationProperty, mixed $value): string
    {
        return $annotationProperty->isEncrypted() && !empty($value)
            ? $this->encryptor->decrypt((string) $value, $annotationProperty->getEncryptionOptions())
            : (string) $value;
    }
}
