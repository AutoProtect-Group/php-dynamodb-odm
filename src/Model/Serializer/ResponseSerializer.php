<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Serializer;

use DateTimeInterface;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Types\BooleanType;
use Autoprotect\DynamodbODM\Annotation\Types\CollectionType;
use Autoprotect\DynamodbODM\Annotation\Types\DateType;
use Autoprotect\DynamodbODM\Annotation\Types\EnumType;
use Autoprotect\DynamodbODM\Annotation\Types\FloatType;
use Autoprotect\DynamodbODM\Annotation\Types\HashMapType;
use Autoprotect\DynamodbODM\Annotation\Types\IntegerType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelType;
use Autoprotect\DynamodbODM\Annotation\Types\MoneyType;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Annotation\Types\Money;

/**
 * Class ResponseSerializer
 *
 * @package Autoprotect\DynamodbODM\Model\Serializer
 */
class ResponseSerializer extends Serializer
{
    protected function processValue(
        PropertyInterface $annotationProperty,
        mixed $value
    ): null|array|string|int|float|bool {
        return match ($annotationProperty->getType()) {
            ModelType::TYPE_NAME => (function (ModelInterface $model): ?array {
                $processedValue = $this->serialize($model);

                if (!array_filter($processedValue, fn ($modelField) => !is_null($modelField))) {
                    return null;
                }

                return $processedValue;
            })($value),
            CollectionType::TYPE_NAME, HashMapType::TYPE_NAME => array_values(array_map(
                function (ModelInterface $model): array {
                    return $this->serialize($model);
                },
                $value
            )),
            MoneyType::TYPE_NAME => round($value, 2),
            Money::TYPE_NAME => $value->jsonSerialize(),
            DateType::TYPE_NAME => $value->format(DateTimeInterface::ATOM),
            IntegerType::TYPE_NAME => (int) $value,
            FloatType::TYPE_NAME => (float) $value,
            BooleanType::TYPE_NAME => (bool) $value,
            EnumType::TYPE_NAME => empty($value) ? null : $value->toScalar(),
            default => $value,
        };
    }

    protected function encryptField(
        PropertyInterface $annotationProperty,
        null|string|array $value
    ): null|string|array {
        // no need to encrypt anything in response serializer
        return $value;
    }
}
