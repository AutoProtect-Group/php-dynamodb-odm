<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Types\StringType;

class DuplicatedPrivatePropertyChildModelClass extends DuplicatedPrivatePropertyParentModelClass
{
    public function __construct(
        #[StringType]
        private string $providerName,
        #[StringType]
        private string $policyId,
        string $applicationId,
        #[StringType]
        private string $dealId,
        #[StringType]
        private string $category
    ) {
        parent::__construct($providerName, $policyId, $applicationId, null);
    }
}