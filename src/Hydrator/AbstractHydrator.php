<?php

namespace Autoprotect\DynamodbODM\Hydrator;

use DateTime;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Types\EnumType;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date\DateTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date\DateTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Enum\EnumTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Enum\EnumTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Float\FloatTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Float\FloatTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\HashMap\HashMapTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\HashMap\HashMapTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Integer\IntegerTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Integer\IntegerTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model\ModelTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model\ModelTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ModelCollection\ModelCollectionTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ModelCollection\ModelCollectionTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money\MoneyTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money\MoneyTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ScalarCollection\ScalarCollectionTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ScalarCollection\ScalarCollectionTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\String\StringTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\String\StringTypeHydratorInterface;
use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Annotation\Types\Money;
use Autoprotect\DynamodbODM\Annotation\Types\DateType;
use Autoprotect\DynamodbODM\Annotation\Types\FloatType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelType;
use Autoprotect\DynamodbODM\Annotation\Types\MoneyType;
use Autoprotect\DynamodbODM\Annotation\Types\BooleanType;
use Autoprotect\DynamodbODM\Annotation\Types\HashMapType;
use Autoprotect\DynamodbODM\Annotation\Types\IntegerType;
use Autoprotect\DynamodbODM\Annotation\Types\CollectionType;
use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Annotation\Types\ScalarCollectionType;
use Autoprotect\DynamodbODM\Hydrator\Exception\IdSetterDoesNotExistException;
use Autoprotect\DynamodbODM\Hydrator\Exception\PropertySetterMethodDoesNotExistException;

/**
 * Class Hydrator
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Hydrator
 */
abstract class AbstractHydrator implements HydratorInterface
{
    protected const SET_METHOD_PREFIX = 'set';

    protected ModelCollectionTypeHydratorInterface $modelCollectionTypeHydrator;
    protected ModelTypeHydratorInterface $modelTypeHydrator;
    protected ScalarCollectionTypeHydratorInterface $scalarCollectionTypeHydrator;
    protected HashMapTypeHydratorInterface $hashMapTypeHydrator;
    protected MoneyTypeHydratorInterface $moneyTypeHydrator;
    protected IntegerTypeHydratorInterface $integerTypeHydrator;
    protected FloatTypeHydratorInterface $floatTypeHydrator;
    protected DateTypeHydratorInterface $dateTypeHydrator;
    protected EnumTypeHydratorInterface $enumTypeHydrator;
    protected StringTypeHydratorInterface $stringTypeHydrator;

    public function __construct(
        protected string $modelClassName,
        protected AnnotationManagerInterface $annotationManager,
        protected ?EncryptorInterface $encryptor = null,
        ?ModelTypeHydratorInterface $modelTypeHydrator = null,
        ?ModelCollectionTypeHydratorInterface $modelCollectionTypeHydrator = null,
        ?ScalarCollectionTypeHydratorInterface $scalarCollectionTypeHydrator = null,
        ?HashMapTypeHydratorInterface $hashMapTypeHydrator = null,
        ?MoneyTypeHydratorInterface $moneyTypeHydrator = null,
        ?IntegerTypeHydratorInterface $integerTypeHydrator = null,
        ?FloatTypeHydratorInterface $floatTypeHydrator = null,
        ?DateTypeHydratorInterface $dateTypeHydrator = null,
        ?EnumTypeHydratorInterface $enumTypeHydrator = null,
        ?StringTypeHydratorInterface $stringTypeHydrator = null,
    ) {
        $this->modelTypeHydrator = $modelCollectionTypeHydrator ?? new ModelTypeHydrator();
        $this->modelCollectionTypeHydrator = $modelTypeHydrator
            ?? new ModelCollectionTypeHydrator($this->modelTypeHydrator);
        $this->scalarCollectionTypeHydrator = $scalarCollectionTypeHydrator
            ?? new ScalarCollectionTypeHydrator($this->encryptor);
        $this->hashMapTypeHydrator = $hashMapTypeHydrator ?? new HashMapTypeHydrator($this->modelTypeHydrator);
        $this->moneyTypeHydrator = $moneyTypeHydrator ?? new MoneyTypeHydrator();
        $this->integerTypeHydrator = $integerTypeHydrator ?? new IntegerTypeHydrator();
        $this->floatTypeHydrator = $floatTypeHydrator ?? new FloatTypeHydrator();
        $this->dateTypeHydrator = $dateTypeHydrator ?? new DateTypeHydrator();
        $this->enumTypeHydrator = $enumTypeHydrator ?? new EnumTypeHydrator();
        $this->stringTypeHydrator = $stringTypeHydrator ?? new StringTypeHydrator($this->encryptor);
    }

    public function setClassName(string $modelClassName): static
    {
        $this->modelClassName = $modelClassName;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function hydrate(
        array $data,
        ?string $discriminatorFieldName = null,
        ?ModelInterface $existingModel = null
    ): ModelInterface;

    /**
     * Get value from raw data
     *
     * @param PropertyInterface $annotationProperty
     * @param mixed             $value
     *
     * @return DateTime|float|int|string|array|ModelInterface
     *
     * @throws \Exception
     */
    protected function processValue(PropertyInterface $annotationProperty, mixed $value): mixed
    {
        $fieldName = $annotationProperty->getName();

        return match ($annotationProperty->getType()) {
            ModelType::TYPE_NAME => $this->modelTypeHydrator->hydrate(
                $this->getAnnotationModelClassNameByFieldName($fieldName),
                $this,
                $fieldName,
                $value,
            ),
            CollectionType::TYPE_NAME => $this->modelCollectionTypeHydrator->hydrate(
                $this->getAnnotationModelClassNameByFieldName($fieldName),
                $this,
                $fieldName,
                $value
            ),
            ScalarCollectionType::TYPE_NAME => $this->scalarCollectionTypeHydrator->hydrate(
                $annotationProperty,
                $value
            ),
            HashMapType::TYPE_NAME => $this->hashMapTypeHydrator->hydrate(
                $this->getAnnotationModelClassNameByFieldName($fieldName),
                $this,
                $fieldName,
                $value,
            ),
            // legacy type. should not be used anywhere
            MoneyType::TYPE_NAME => (function (string $value): float {
                trigger_error(
                    sprintf("Usage of legacy %s type is deprecated", MoneyType::TYPE_NAME),
                    E_USER_DEPRECATED
                );
                return round(bcdiv($value, 100.0, 2), 2);
            })($value),
            Money::TYPE_NAME => $this->moneyTypeHydrator->hydrate($value),
            IntegerType::TYPE_NAME => $this->integerTypeHydrator->hydrate($value),
            FloatType::TYPE_NAME => $this->floatTypeHydrator->hydrate($value),
            DateType::TYPE_NAME => $this->dateTypeHydrator->hydrate($value),
            BooleanType::TYPE_NAME => (bool) $value,
            EnumType::TYPE_NAME => $this->enumTypeHydrator->hydrate(
                $this->modelClassName,
                $annotationProperty,
                $value,
            ),
            default => $this->stringTypeHydrator->hydrate($annotationProperty, $value)
        };
    }

    /**
     * Get model object using input data
     *
     * @param array $data
     *
     * @param string|null $discriminatorFieldName
     * @return ModelInterface
     */
    protected function getModel(array $data, ?string $discriminatorFieldName = null): ModelInterface
    {
        $this->modelClassName = $this->getModelNameUsingParams($data, $discriminatorFieldName);

        return new $this->modelClassName;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getPropertyMethodName(string $property): string
    {
        return $this->getMethodName($property, PropertySetterMethodDoesNotExistException::class);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getPrimaryKeyMethodName(string $property): string
    {
        return $this->getMethodName($property, IdSetterDoesNotExistException::class);
    }

    /**
     * @param string $property
     * @param string $exceptionClassName
     *
     * @return string
     */
    private function getMethodName(string $property, string $exceptionClassName): string
    {
        $setterName = self::SET_METHOD_PREFIX . ucfirst($property);

        if (!method_exists($this->modelClassName, $setterName)) {
            throw new $exceptionClassName($this->modelClassName);
        }

        return $setterName;
    }

    /**
     * Get ID property name
     *
     * @return string
     */
    protected function getIdPropertyName(): string
    {
        return $this->annotationManager->getPrimaryKeyByModelClassName($this->modelClassName);
    }

    /**
     * Get all property names with types
     *
     * @return array
     */
    protected function getFieldTypes(): array
    {
        return $this->annotationManager->getFieldTypesByModelClassName($this->modelClassName);
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    protected function getAnnotationModelClassNameByFieldName(string $fieldName): string
    {
        return $this->annotationManager->getAnnotationModelClassNameByFieldName($this->modelClassName, $fieldName);
    }

    /**
     * @param array $data
     * @param string|null $discriminatorFieldName
     *
     * @return string
     */
    protected function getModelNameUsingParams(array $data, ?string $discriminatorFieldName): string
    {
        return $this->annotationManager->getModelNameUsingParams($this->modelClassName, $data, $discriminatorFieldName);
    }
}
