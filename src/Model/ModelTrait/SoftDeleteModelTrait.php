<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model\ModelTrait;

use Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Trait SoftDeleteModelTrait
 *
 * @package Autoprotect\DynamodbODM\Model\ModelTrait
 */
trait SoftDeleteModelTrait
{
    /**
     * @var bool
     *
     * @Types\BooleanType
     */
    protected bool $isDeleted = false;

    /**
     * @param bool $isDeleted
     *
     * @return mixed
     */
    public function setIsDeleted(?bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }
}
