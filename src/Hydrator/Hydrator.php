<?php

namespace Autoprotect\DynamodbODM\Hydrator;

use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Annotation\Encryption\EncryptedPropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Hydrator\Exception\PrimaryKeyException;
use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Class Hydrator
 *
 * @package Autoprotect\DynamodbODM\Hydrator
 */
class Hydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function hydrate(
        array $data,
        ?string $discriminatorFieldName = null,
        ?ModelInterface $existingModel = null
    ): ModelInterface {
        $model = $existingModel ?? $this->getModel($data, $discriminatorFieldName);

        $modelProperties = $this->getFieldTypes();

        /** @var PropertyInterface $annotationProperty */
        foreach ($modelProperties as $annotationProperty) {
            if (!isset($data[$annotationProperty->getName()])) {
                continue;
            }

            $setterName = $this->getPropertyMethodName($annotationProperty->getName());

            $model->$setterName(
                $this->processValue(
                    $annotationProperty,
                    $data[$annotationProperty->getName()],
                )
            );
        }

        return $model;
    }
}
