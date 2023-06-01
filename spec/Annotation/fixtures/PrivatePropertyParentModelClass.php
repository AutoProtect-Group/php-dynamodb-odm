<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Types\StringType;

class PrivatePropertyParentModelClass
{
    /**
     * @param string|null $reason
     * @param string|null $type
     */
    public function __construct(
        #[StringType]
        private ?string $reason = null,
        #[StringType]
        private ?string $type = null
    ) {}

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}