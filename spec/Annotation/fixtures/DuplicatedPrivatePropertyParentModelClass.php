<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Types\StringType;

class DuplicatedPrivatePropertyParentModelClass extends BaseDuplicatedPrivatePropertyParentModelClass
{
    /**
     * @param string $providerName
     * @param string $policyId
     * @param string $applicationId
     * @param string|null $reason
     * @param string|null $type
     */
    public function __construct(
        #[StringType]
        private string $providerName,
        #[StringType]
        private string $policyId,
        string $applicationId,
        #[StringType]
        private ?string $reason = null,
        #[StringType]
        private ?string $type = null
    ) {
        parent::__construct($providerName);
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->providerName;
    }

    /**
     * @return string
     */
    public function getPolicyId(): string
    {
        return $this->policyId;
    }

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

    /**
     * @param string $providerName
     */
    public function setProviderName(string $providerName): void
    {
        $this->providerName = $providerName;
    }
}