<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumType implements TypeInterface, EnumInterface
{
    public const TYPE_NAME = 'enum';

    public function __construct(
        /** If it's a backed enum then we need to know if we need to use tryFrom or just from */
        protected readonly bool $isStrict = true,
    ) {
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
    public function isStrict(): bool
    {
        return $this->isStrict;
    }
}
