<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Serializer;

use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Model\Collection\CollectionInterface;
use Autoprotect\DynamodbODM\Model\Collection\HashMap;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use InvalidArgumentException;

/**
 * Class Serializer
 *
 * @package Autoprotect\DynamodbODM\Model\Serializer
 */
class Serializer extends AbstractSerializer
{
    /**
     * {@inheritDoc}
     */
    public function serialize(array | CollectionInterface | ModelInterface $value): array
    {
        if ($value instanceof CollectionInterface) {
            $response = [];

            foreach ($value->getModels() as $model) {
                $response[] = $this->serializeModel($model);
            }

            return $response;
        }

        if (is_array($value)) {
            return array_map(
                function (ModelInterface $model): array {
                    return $this->serializeModel($model);
                },
                array_filter(
                    $value,
                    function ($model): bool {
                        return $model instanceof ModelInterface;
                    }
                )
            );
        }

        return $this->serializeModel($value);
    }
}
