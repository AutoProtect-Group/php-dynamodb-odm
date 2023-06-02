<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Encryption\EncryptedPropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Exception\AnnotationLogicException;
use Autoprotect\DynamodbODM\Annotation\Exception\DuplicatePrivatePropertyException;
use Autoprotect\DynamodbODM\Annotation\Exception\NoPropertyFoundException;
use Autoprotect\DynamodbODM\Annotation\Key\Primary;
use Autoprotect\DynamodbODM\Annotation\Key\Sort;
use Autoprotect\DynamodbODM\Annotation\Property\AnnotationProperty;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Types\ModelTypeInterface;
use Autoprotect\DynamodbODM\Annotation\Types\TypeInterface;
use Autoprotect\DynamodbODM\Model\Exception\ModelDiscriminatorException;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Exception;
use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class ModelAnnotationProcessor
 *
 * @package Autoprotect\DynamodbODM\Annotation
 */
class ModelAnnotationProcessor implements ModelAnnotationProcessorInterface
{
    protected const DISCRIMINATOR_ATTRIBUTE = 'DISCRIMINATOR_ATTRIBUTE';
    protected const DEFAULT_DISCRIMINATOR_ATTRIBUTE_VALUE = 'DEFAULT_DISCRIMINATOR_ATTRIBUTE_VALUE';
    protected const DISCRIMINATOR_MAP = 'DISCRIMINATOR_MAP';

    protected string $className;
    protected Reader $reader;
    protected array $fields = [];
    protected array $encryptedFields = [];
    protected PropertyInterface $idProperty;
    protected PropertyInterface $sortKeyProperty;

    /**
     * @param string $className
     * @param Reader|null $reader
     *
     * @throws AnnotationException
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(string $className, ?Reader $reader = null)
    {
        $this->className = $className;
        $this->reader = $reader ?? new AnnotationReader();
        $this->parseAnnotations();
    }

    /**
     * Get Parent Class Private Property.
     *
     * @throws ReflectionException
     */
    private function getParentClassPrivateProperties(string $className, array $properties = []): Generator
    {
        $ref = new ReflectionClass($className);
        foreach ($ref->getProperties(ReflectionProperty::IS_PRIVATE) as $prop) {
            yield $prop;
        }

        if ($ref->getParentClass()) {
            yield from $this->getParentClassPrivateProperties($ref->getParentClass()->getName(), $properties);
        }
    }

    /**
     * Collect class properties and collect parent class private properties.
     *
     * @return void
     *
     * @throws Exception
     */
    public function parseAnnotations(): void
    {
        $refClass = new ReflectionClass($this->className);
        foreach ($refClass->getProperties() as $property) {
            $annotationProperty = $this->buildAnnotationProperty($property);

            if (!is_null($annotationProperty)) {
                $this->addFieldType($annotationProperty);
            }
        }

        if (!$refClass->getParentClass()) {
            return;
        }

        foreach ($this->getParentClassPrivateProperties(
            $refClass->getParentClass()->getName()
        ) as $parentProperty) {
            $annotationProperty = $this->buildAnnotationProperty($parentProperty);

            if (!is_null($annotationProperty)) {
                $this->addFieldType($annotationProperty);
            }
        }
    }

    public function getFieldTypes(): array
    {
        return $this->fields;
    }

    public function getEncryptedFields(): array
    {
        return $this->encryptedFields;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getIdProperty(): PropertyInterface
    {
        return $this->idProperty ?? throw new NoPropertyFoundException(
            '',
            sprintf("No id property found in class %s", $this->className)
        );
    }

    public function setIdProperty(AnnotationProperty $annotationProperty): static
    {
        $this->idProperty = $annotationProperty;
        return $this;
    }

    /**
     * @throws ReflectionException
     * @return bool
     *
     */
    public function isImplementsModelInterface(): bool
    {
        return (new \ReflectionClass($this->className))->implementsInterface(ModelInterface::class);
    }

    /**
     * @throws ReflectionException
     * @return string
     *
     */
    public function getClassShortName(): string
    {
        return (new \ReflectionClass($this->className))->getShortName();
    }

    /**
     * @param array $data
     * @param string|null $discriminatorFieldName
     *
     * @throws ReflectionException
     * @return string
     *
     */
    public function getModelNameUsingParams(array $data, ?string $discriminatorFieldName): string
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $discriminatorAttr = $reflectionClass->getConstant(self::DISCRIMINATOR_ATTRIBUTE);
        $defaultDiscriminatorAttr = $reflectionClass->getConstant(self::DEFAULT_DISCRIMINATOR_ATTRIBUTE_VALUE);
        $discriminatorMap = $reflectionClass->getConstant(self::DISCRIMINATOR_MAP);

        if ($discriminatorAttr && $discriminatorMap) {
            //If required discriminator attribute field was not sent and no default discriminator then throw exception
            if (!isset($data[$discriminatorAttr]) && !$defaultDiscriminatorAttr) {
                $this->throwModelDiscriminatorException(
                    'Please provide %s attribute',
                    $discriminatorAttr,
                    $discriminatorFieldName
                );
            }

            $discriminatorAttrValue = $data[$discriminatorAttr] ?? $defaultDiscriminatorAttr;

            //If provided discriminator attribute value not valid then throw exception
            if (!isset($discriminatorMap[$discriminatorAttrValue]) && $reflectionClass->isAbstract()) {
                $this->throwModelDiscriminatorException(
                    'Please provide a valid %s value',
                    $discriminatorAttr,
                    $discriminatorFieldName
                );
            }

            return $discriminatorMap[$discriminatorAttrValue] ?? $this->className;
        }

        return $this->className;
    }

    public function getAnnotationModelClassNameByFieldName(string $fieldName): string
    {
        $modelTypeAnnotationProperty = $this->getAnnotationPropertyByFieldName($fieldName);

        if (!$modelTypeAnnotationProperty->getTypeAnnotation() instanceof ModelTypeInterface) {
            throw new AnnotationLogicException(
                sprintf("The property %s of the class %s is not a Model type", $fieldName, $this->className)
            );
        }

        return $modelTypeAnnotationProperty->getTypeAnnotation()->getModelClassName();
    }

    public function getSortKeyProperty(): PropertyInterface
    {
        return $this->sortKeyProperty
            ?? throw new NoPropertyFoundException(
                '',
                sprintf("No sort key found in the class %s", $this->className)
            );
    }

    public function setSortKeyProperty(AnnotationProperty $sortKeyProperty): static
    {
        $this->sortKeyProperty = $sortKeyProperty;
        return $this;
    }

    protected function addEncryptedProperty(PropertyInterface $encryptedAnnotationProperty): static
    {
        $this->encryptedFields[$encryptedAnnotationProperty->getName()] = $encryptedAnnotationProperty;
        return $this;
    }

    protected function getAnnotationPropertyByFieldName(string $propertyName): AnnotationProperty
    {
        return $this->fields[$propertyName] ?? throw new NoPropertyFoundException($propertyName);
    }

    protected function buildAnnotationProperty(ReflectionProperty $property): ?AnnotationProperty
    {
        $annotations = $this->reader->getPropertyAnnotations($property);

        $reflectionType = $property->getType();

        if (empty($annotations) || is_null($reflectionType)) {
            return null;
        }

        [$annotationProperty, $dynamoDbAnnotationsFound] = array_reduce(
            $annotations,
            function (array $builtAnnotationProperty, object $annotation): array {
                [$annotationProperty, $dynamoDbAnnotationsFound] = $builtAnnotationProperty;

                if ($annotation instanceof TypeInterface) {
                    return [
                        $annotationProperty
                        ->setType($annotation->getType())
                        ->setTypeAnnotation($annotation),
                        true
                    ];
                }

                if ($annotation instanceof Primary) {
                    $this->setIdProperty($annotationProperty);
                    return [$annotationProperty->setIsPrimary(true), true];
                }

                if ($annotation instanceof Sort) {
                    $this->setSortKeyProperty($annotationProperty);
                    return [$annotationProperty->setIsSortKey(true), true];
                }

                if ($annotation instanceof EncryptedPropertyInterface) {
                    $this->addEncryptedProperty($annotationProperty);
                    return [
                        $annotationProperty
                        ->setIsEncrypted(true)
                        ->setEncryptionOptions($annotation->getOptions()),
                        true
                    ];
                }

                return [$annotationProperty, $dynamoDbAnnotationsFound];
            },
            [new AnnotationProperty($property->getName(), $reflectionType), false]
        );

        return $dynamoDbAnnotationsFound ? $annotationProperty : null;
    }

    /**
     * @param AnnotationProperty $fieldType
     *
     * @return $this
     *
     * @throws DuplicatePrivatePropertyException
     */
    protected function addFieldType(AnnotationProperty $fieldType): static
    {
        if (isset($this->fields[$fieldType->getName()])) {
            throw new DuplicatePrivatePropertyException($fieldType->getName()) ;
        }

        $this->fields[$fieldType->getName()] = $fieldType;

        return $this;
    }

    /**
     * @param string $errorMessage
     * @param string $discriminatorAttr
     * @param string|null $discriminatorFieldName
     */
    private function throwModelDiscriminatorException(
        string $errorMessage,
        string $discriminatorAttr,
        ?string $discriminatorFieldName
    ) {
        $errorMessageParam = $discriminatorFieldName ?
            sprintf('%s %s', $discriminatorFieldName, $discriminatorAttr) : $discriminatorAttr;
        throw new ModelDiscriminatorException(sprintf($errorMessage, $errorMessageParam));
    }
}
