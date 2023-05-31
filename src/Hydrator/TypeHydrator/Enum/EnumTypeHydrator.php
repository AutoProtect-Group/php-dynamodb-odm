<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Enum;

use BackedEnum;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Hydrator\Exception\InvalidPropertyTypeException;
use Autoprotect\DynamodbODM\Hydrator\Exception\PropertyShouldBeOfNamedTypeException;
use Autoprotect\DynamodbODM\Model\EnumerationInterface;
use Autoprotect\DynamodbODM\Model\UnitEnumerationInterface;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Throwable;

class EnumTypeHydrator implements EnumTypeHydratorInterface
{
    public function hydrate(
        string $modelClassName,
        PropertyInterface $annotationProperty,
        int|string $enum
    ): ?EnumerationInterface {
        if ($annotationProperty->getReflectionType() instanceof ReflectionUnionType) {
            return $this->processUnionType($modelClassName, $annotationProperty, $enum);
        }

        return $this->processSingularEnum($modelClassName, $annotationProperty, $enum);
    }

    protected function processSingularEnum(
        string $modelClassName,
        PropertyInterface $annotationProperty,
        int|string $enum
    ): ?EnumerationInterface {
        $enumClassname = $this->getEnumClassname(
            $modelClassName,
            $annotationProperty->getReflectionType(),
            $annotationProperty->getName()
        );

        return $annotationProperty->getTypeAnnotation()->isStrict()
            ? $enumClassname::from($enum)
            : $enumClassname::tryFrom($enum);
    }

    protected function processUnionType(
        string $modelClassName,
        PropertyInterface $annotationProperty,
        int|string $enum
    ): ?EnumerationInterface {
        $latestException = null;
        foreach ($annotationProperty->getReflectionType()->getTypes() as $type) {
            $enumClassname = $this->getEnumClassname($modelClassName, $type, $annotationProperty->getName());

            if ($annotationProperty->getTypeAnnotation()->isStrict()) {
                try {
                    return $enumClassname::from($enum);
                } catch (Throwable $exception) {
                    $latestException = $exception;
                }
            } elseif (($value = $enumClassname::tryFrom($enum)) !== null) {
                return $value;
            }
        }
        if (!$annotationProperty->getReflectionType()->allowsNull() && $latestException !== null) {
            throw $latestException;
        }
        return null;
    }

    protected function getEnumClassname(
        string $modelClassName,
        ReflectionType $reflectionType,
        string $annotationPropertyName
    ): UnitEnumerationInterface|BackedEnum|string {
        if (!$reflectionType instanceof ReflectionNamedType) {
            throw new PropertyShouldBeOfNamedTypeException(
                sprintf(
                    'Property %s should be ReflectionNamedType, '.
                    'e. g. should have only enum type in the class %s',
                    $annotationPropertyName,
                    $modelClassName
                )
            );
        }
        $name = $reflectionType->getName();
        if (!$reflectionType->allowsNull()
            &&
            !(
                (
                    is_subclass_of($name, UnitEnumerationInterface::class)
                    || is_subclass_of($name, BackedEnum::class)
                )
                && is_subclass_of($name, EnumerationInterface::class)
            )
        ) {
            throw new InvalidPropertyTypeException(
                $annotationPropertyName,
                [UnitEnumerationInterface::class, BackedEnum::class, EnumerationInterface::class],
                $modelClassName
            );
        }

        return $name;
    }
}
