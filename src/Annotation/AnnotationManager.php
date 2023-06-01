<?php

namespace Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Exception\NoPropertyFoundException;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Doctrine\Common\Annotations\Reader;

/**
 * Class AnnotationManager
 *
 * @package Autoprotect\DynamodbODM\Annotation
 */
class AnnotationManager implements AnnotationManagerInterface
{
    protected const ID_KEY_NAME_FALLBACK_DEFAULT = 'id';

    protected array $annotationProcessors = [];

    public function __construct(
        protected ?Reader $annotationReader = null
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $className
     * @return array
     */
    public function getFieldTypesByModelClassName(string $className): array
    {
        return $this->getAnnotationProcessorByClassName($className)->getFieldTypes();
    }

    public function getPrimaryKeyByModelClassName(string $className): string
    {
        try {
            return $this->getAnnotationProcessorByClassName($className)->getIdProperty()->getName();
        } catch (NoPropertyFoundException) {
            // we need this for backward compatibility
            return static::ID_KEY_NAME_FALLBACK_DEFAULT;
        }
    }

    public function isImplementsModelInterface(string $className): bool
    {
        return $this->getAnnotationProcessorByClassName($className)->isImplementsModelInterface();
    }

    public function getClassShortName(string $className): string
    {
        return $this->getAnnotationProcessorByClassName($className)->getClassShortName();
    }

    public function getAnnotationModelClassNameByFieldName(string $className, string $fieldName): string
    {
        return $this->getAnnotationProcessorByClassName($className)->getAnnotationModelClassNameByFieldName($fieldName);
    }

    /**
     * @param string $className
     * @param array $data
     *
     * @param string|null $discriminatorFieldName
     * @return string
     */
    public function getModelNameUsingParams(string $className, array $data, ?string $discriminatorFieldName): string
    {
        return $this->getAnnotationProcessorByClassName($className)
            ->getModelNameUsingParams($data, $discriminatorFieldName);
    }

    /**
     * {@inheritDoc}
     */
    public function getSortKeyByModelClassName(string $className): PropertyInterface
    {
        return $this->getAnnotationProcessorByClassName($className)->getSortKeyProperty();
    }

    /**
     * {@inheritDoc}
     */
    public function getEncryptedFieldsByClassName(string $className): array
    {
        return $this->getAnnotationProcessorByClassName($className)->getEncryptedFields();
    }

    /**
     * @throws \ReflectionException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    protected function getAnnotationProcessorByClassName(string $className): ModelAnnotationProcessorInterface
    {
        $this->annotationProcessors[$className] ??= new ModelAnnotationProcessor($className, $this->annotationReader);
        return $this->annotationProcessors[$className];
    }
}
