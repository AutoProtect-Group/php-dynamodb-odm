<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

class TestAsset extends Asset
{
    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $engineType;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $code;

    /**
     * @return string
     */
    public function getEngineType(): string
    {
        return $this->engineType;
    }

    /**
     * @param string $engineType
     *
     * @return TestAsset
     */
    public function setEngineType(string $engineType): self
    {
        $this->engineType = $engineType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return TestAsset
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
