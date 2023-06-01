<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Property;

use Autoprotect\DynamodbODM\Annotation\Exception\NoEncryptionOptionsDefinedForProperty;
use Autoprotect\DynamodbODM\Annotation\Exception\NoTypeDefinedForPropertyException;
use Autoprotect\DynamodbODM\Annotation\Types\EnumInterface;
use Autoprotect\DynamodbODM\Annotation\Types\ModelTypeInterface;
use Autoprotect\DynamodbODM\Annotation\Types\TypeInterface;
use ReflectionType;

class AnnotationProperty implements PropertyInterface
{
    protected string $type;
    protected bool $isPrimary = false;
    protected bool $isSortKey = false;
    protected bool $isEncrypted = false;
    protected array $encryptionOptions;
    protected TypeInterface|ModelTypeInterface $typeAnnotation;

    public function __construct(
        protected string $name,
        protected ReflectionType $reflectionType,
    ) {
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function isSortKey(): bool
    {
        return $this->isSortKey;
    }

    public function setIsSortKey(bool $isSortKey): static
    {
        $this->isSortKey = $isSortKey;
        return $this;
    }

    public function isEncrypted(): bool
    {
        return $this->isEncrypted;
    }

    public function setIsEncrypted(bool $isEncrypted): static
    {
        $this->isEncrypted = $isEncrypted;
        return $this;
    }

    public function getEncryptionOptions(): array
    {
        return $this->encryptionOptions ?? throw new NoEncryptionOptionsDefinedForProperty($this->getName());
    }

    public function setEncryptionOptions(?array $encryptionOptions): static
    {
        $this->encryptionOptions = $encryptionOptions;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type ?? throw new NoTypeDefinedForPropertyException(
            sprintf("No type annotation is found for the property %s", $this->getName())
        );
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeAnnotation(): ModelTypeInterface|TypeInterface|EnumInterface
    {
        return $this->typeAnnotation;
    }

    public function setTypeAnnotation(ModelTypeInterface|TypeInterface|EnumInterface $typeAnnotation): static
    {
        $this->typeAnnotation = $typeAnnotation;
        return $this;
    }

    public function getReflectionType(): ReflectionType
    {
        return $this->reflectionType;
    }
}
