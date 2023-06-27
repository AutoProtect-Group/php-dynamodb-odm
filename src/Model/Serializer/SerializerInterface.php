<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\Serializer;

use Autoprotect\DynamodbODM\Model\Collection\CollectionInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;

/**
 * Interface SerializerInterface
 *
 * @package Autoprotect\DynamodbODM\Model\Serializer
 */
interface SerializerInterface
{
    /**
     * @param array|CollectionInterface|ModelInterface $value
     *
     * @return array
     */
    public function serialize(array | CollectionInterface | ModelInterface $value): array;
}
