<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Class NewModel
 *
 * @package spec\Autoprotect\DynamodbODM\Annotation\fixtures
 */
class NewModel extends Model
{
    /**
     * @var string
     *
     * @Key\Primary
     * @Types\StringType
     */
    protected string $id;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $name;

    /**
     * @var float
     *
     * @Types\NumberType
     */
    protected float $price;

    /**
     * @var bool
     *
     * @Types\BooleanType
     */
    protected bool $isValid;

    /**
     * {@inheritDoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
