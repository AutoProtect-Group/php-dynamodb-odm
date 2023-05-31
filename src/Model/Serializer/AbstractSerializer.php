<?php

namespace Autoprotect\DynamodbODM\Model\Serializer;

use DateTimeInterface;
use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Annotation\Exception\NoTypeDefinedForPropertyException;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Types\BooleanType;
use Autoprotect\DynamodbODM\Annotation\Types\EnumType;
use Autoprotect\DynamodbODM\Annotation\Types\HashMapType;
use Autoprotect\DynamodbODM\Annotation\Types\CollectionType;
use Autoprotect\DynamodbODM\Annotation\Types\DateType;
use Autoprotect\DynamodbODM\Annotation\Types\FloatType;
use Autoprotect\DynamodbODM\Annotation\Types\IntegerType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelTypeInterface;
use Autoprotect\DynamodbODM\Annotation\Types\MoneyType;
use Autoprotect\DynamodbODM\Annotation\Types\ScalarCollectionType;
use Autoprotect\DynamodbODM\Annotation\Types\StringType;
use Autoprotect\DynamodbODM\Annotation\Types\TypeInterface;
use Autoprotect\DynamodbODM\Model\Collection\CollectionInterface;
use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;
use Autoprotect\DynamodbODM\Model\EnumerationInterface;
use Autoprotect\DynamodbODM\Model\Exception\EmptyModelPropertyException;
use Autoprotect\DynamodbODM\Model\Exception\GetMethodNotFoundException;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Annotation\Types\Money;
use ReflectionType;

/**
 * Class AbstractSerializer
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Model\Serializer
 */
abstract class AbstractSerializer implements SerializerInterface
{
    protected const METHOD_PREFIX_GET = 'get';
    protected const METHOD_PREFIX_IS = 'is';

    /**
     * This is temporary stub string property needed to process nested scalar arrays
     *
     * @var PropertyInterface
     */
    protected PropertyInterface $stubStringPropertyInterface;

    public function __construct(
        protected AnnotationManagerInterface $annotationManager,
        protected ?EncryptorInterface $encryptor = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    abstract public function serialize(array | CollectionInterface | ModelInterface $value): array;

    protected function serializeModel(ModelInterface $model): array
    {
        $modelClassName = $model::class;
        $modelProperties = $this->annotationManager->getFieldTypesByModelClassName($modelClassName);

        if (empty($modelProperties)) {
            throw new EmptyModelPropertyException($modelClassName);
        }

        return array_reduce(
            $modelProperties,
            function (array $serialized, PropertyInterface $annotationProperty) use ($model, $modelClassName): array {
                $methodName = $this->getMethodName($modelClassName, $annotationProperty->getName());

                $serializedValue = $this->getProcessedValue($annotationProperty, $model->$methodName());

                $serialized[$annotationProperty->getName()] = $serializedValue;

                return $serialized;
            },
            []
        );
    }

    protected function encryptField(
        PropertyInterface $annotationProperty,
        null|string|array $value
    ): null|string|array {
        if (empty($value)) {
            return $value;
        }

        return $this->encryptor->encrypt($value, $annotationProperty->getEncryptionOptions());
    }

    /**
     * @param string $modelName
     * @param string $property
     *
     * @return string
     */
    protected function getMethodName(string $modelName, string $property): string
    {
        $getMethod = static::METHOD_PREFIX_GET . ucfirst($property);

        if (!method_exists($modelName, $getMethod)) {
            $isMethod = static::METHOD_PREFIX_IS . ucfirst($property);

            if (!method_exists($modelName, $isMethod)) {
                throw new GetMethodNotFoundException(
                    sprintf(GetMethodNotFoundException::MESSAGE_DEFAULT, $getMethod, $isMethod, $modelName)
                );
            }
        }

        return $isMethod ?? $getMethod;
    }

    protected function getProcessedValue(
        PropertyInterface $annotationProperty,
        mixed $fieldValue
    ): null|array|string|int|float|bool {
        if ($fieldValue === null) {
            return null;
        }

        return $this->processValue($annotationProperty, $fieldValue);
    }

    protected function processValue(
        PropertyInterface $annotationProperty,
        mixed $value
    ): null|array|string|int|float|bool {
        return match ($annotationProperty->getType()) {
            ModelType::TYPE_NAME => $this->serialize($value),
            CollectionType::TYPE_NAME
                => array_map(fn (ModelInterface $model): array => $this->serialize($model), $value),
            ScalarCollectionType::TYPE_NAME
                => (function (array $scalarArray) use ($annotationProperty): array {
                    array_walk_recursive($scalarArray, function (mixed &$item) {
                        $item = is_string($item)
                            ? $this->processValue($this->getStubStringAnnotationProperty(), $item)
                            : $item;
                    });
                    if ($annotationProperty->isEncrypted()) {
                        return $this->encryptField($annotationProperty, $scalarArray);
                    }
                    return $scalarArray;
                })($value),
            HashMapType::TYPE_NAME => array_reduce(
                $value,
                function (array $processedValue, ModelInterface $model): array {
                    $id = $model->getId();
                    $id = $id instanceof EnumerationInterface ? $id->toScalar() : $id;
                    $processedValue[$id] = $this->serialize($model);
                    return $processedValue;
                },
                []
            ),
            MoneyType::TYPE_NAME => (int) round(bcmul($value, 100.0, 1), 0),
            Money::TYPE_NAME => $value->jsonSerialize(),
            DateType::TYPE_NAME => $value->format(DateTimeInterface::ATOM),
            IntegerType::TYPE_NAME => (int) $value,
            FloatType::TYPE_NAME => (float) $value,
            BooleanType::TYPE_NAME => (bool) $value,
            StringType::TYPE_NAME => ($value === '')
                ? null
                : ($annotationProperty->isEncrypted()
                    ? $this->encryptField($annotationProperty, $value)
                    : $value
                ),
            EnumType::TYPE_NAME => empty($value) ? null : $value->toScalar(),
            default => empty($value) ? null : $value,
        };
    }

    protected function getStubStringAnnotationProperty(): PropertyInterface
    {
        return $this->stubStringPropertyInterface ?? (fn (): PropertyInterface =>
            $this->stubStringPropertyInterface = new class implements PropertyInterface {
                public function getType(): string
                {
                    return StringType::TYPE_NAME;
                }

                public function getName(): string
                {
                    return '';
                }

                public function isPrimary(): bool
                {
                    return false;
                }

                public function isSortKey(): bool
                {
                    return false;
                }

                public function isEncrypted(): bool
                {
                    return false;
                }

                public function getEncryptionOptions(): array
                {
                    return [];
                }

                public function getTypeAnnotation(): ModelTypeInterface|TypeInterface
                {
                    throw new NoTypeDefinedForPropertyException("Stub property doesn't need to have an annotation");
                }

                public function getReflectionType(): ReflectionType
                {
                    throw new NoTypeDefinedForPropertyException(
                        "Stub property doesn't need to have an annotation or named reflection type"
                    );
                }
            })();
    }
}
