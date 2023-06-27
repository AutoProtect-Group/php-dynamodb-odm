<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\RelatedModel;

/**
 * Class IntegerType
 *
 * @package Autoprotect\DynamodbODM\Annotation\Types
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @Attributes({
 *   @Attribute("modelClassName", type = "string"),
 * })
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ModelType implements TypeInterface, ModelTypeInterface
{
    public const TYPE_NAME = 'model';
    public const MODEL_CLASS_NAME = 'modelClassName';

    /**
     * @Required
     */
    protected $modelClassName;

    /**
     * ModelType constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->modelClassName = $values[self::MODEL_CLASS_NAME];
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return static::TYPE_NAME;
    }
    /**
     * {@inheritDoc}
     */
    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }
}
