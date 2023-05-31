<?php

namespace Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;

/**
 * Interface AnnotationManagerInterface
 *
 * @package DealTrak\Adapter\DynamoDBAdapter\Annotation
 */
interface AnnotationManagerInterface
{
    /**
     * Get field types
     *
     * @param string $className
     *
     * @return PropertyInterface[]
     */
    public function getFieldTypesByModelClassName(string $className): array;

    /**
     * Get primary key by given model class name
     *
     * @param string $className
     * @return string
     */
    public function getPrimaryKeyByModelClassName(string $className): string;

    /**
     * Get sort key (composite key) by model class name
     *
     * @param string $className
     * @return string
     */
    public function getSortKeyByModelClassName(string $className): PropertyInterface;

    /**
     * Get a list of encrypted fields
     *
     * @param string $className
     *
     * @return PropertyInterface[]
     */
    public function getEncryptedFieldsByClassName(string $className): array;
}
