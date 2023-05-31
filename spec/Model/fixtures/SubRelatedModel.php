<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

class SubRelatedModel extends Model
{
    /**
     * @var string
     *
     * @Key\Primary
     * @Types\StringType
     */
    protected $id;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return SubRelatedModel
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return SubRelatedModel
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}