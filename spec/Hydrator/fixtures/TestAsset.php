<?php

namespace spec\Autoprotect\DynamodbODM\Hydrator\fixtures;

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
}
