<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Property;

use Autoprotect\DynamodbODM\Annotation\Types\EnumInterface;
use Autoprotect\DynamodbODM\Annotation\Types\ModelTypeInterface;
use Autoprotect\DynamodbODM\Annotation\Types\TypeInterface;
use ReflectionType;

interface PropertyInterface
{
    /**
     * Get a type of the model property
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the name of the model property
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Defines if property is a primary (partition) key
     *
     * @return bool
     */
    public function isPrimary(): bool;

    /**
     * Defines if the property is a sort key
     *
     * @return bool
     */
    public function isSortKey(): bool;

    /**
     * Defines if the property is encrypted inside the database
     *
     * @return bool
     */
    public function isEncrypted(): bool;

    /**
     * Encryption options passed to the encryptor in case there are
     *
     * @return array
     */
    public function getEncryptionOptions(): array;

    /**
     * Get reflection type for defining the type of the property. It's needed to write less code and mappings in the
     * annotations
     *
     * @return ReflectionType
     */
    public function getReflectionType(): ReflectionType;

    /**
     * Get type annotation itself
     *
     * @return ModelTypeInterface|TypeInterface|EnumInterface
     */
    public function getTypeAnnotation(): ModelTypeInterface|TypeInterface|EnumInterface;
}
