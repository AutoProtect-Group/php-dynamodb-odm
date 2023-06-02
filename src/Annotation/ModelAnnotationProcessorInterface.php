<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Property\AnnotationProperty;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;

/**
 * Interface ModelAnnotationProcessorInterface
 *
 * @package Autoprotect\DynamodbODM\Annotation
 */
interface ModelAnnotationProcessorInterface
{
    /**
     * Get field types by the given class name and annotations
     *
     * @return PropertyInterface[]
     */
    public function getFieldTypes(): array;

    /**
     * Get ID key by given annotation
     *
     * @return AnnotationProperty
     */
    public function getIdProperty(): PropertyInterface;

    /**
     * Get Sort key Annotation property
     *
     * @return AnnotationProperty
     */
    public function getSortKeyProperty(): PropertyInterface;

    /**
     * Defines if the model implements ModelInterface
     *
     * @return bool
     */
    public function isImplementsModelInterface(): bool;

    /**
     * Get class short name
     *
     * @return string
     */
    public function getClassShortName(): string;

    /**
     * Get model class name of the model/collection/hashmap typed property. Returns the class name
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getAnnotationModelClassNameByFieldName(string $fieldName): string;

    /**
     * Get list of encrypted annotated fields
     *
     * @return PropertyInterface[]
     */
    public function getEncryptedFields(): array;
}
