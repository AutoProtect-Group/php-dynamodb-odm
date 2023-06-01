<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

abstract class Asset extends Model
{
    public const MODEL_BUILD_PARAM_FIELD = 'type';

    /***
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
    protected string $type;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $mark;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $model;

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
     * @return Asset
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return TestAsset
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMark(): string
    {
        return $this->mark;
    }

    /**
     * @param string $mark
     *
     * @return TestAsset
     */
    public function setMark(string $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     *
     * @return TestAsset
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }
}
