<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Types\StringType;

class BaseDuplicatedPrivatePropertyParentModelClass
{
    public function __construct(
        #[StringType]
        private string $providerName,
    ) {}

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }
}