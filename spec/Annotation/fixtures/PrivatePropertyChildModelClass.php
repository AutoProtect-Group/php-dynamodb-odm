<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use Autoprotect\DynamodbODM\Annotation\Types\StringType;

class PrivatePropertyChildModelClass extends PrivatePropertyParentModelClass
{
    public function __construct(
        #[StringType]
        private string $providerName,
        #[StringType]
        private string $policyId,
        #[StringType]
        private string $dealId,
        #[StringType]
        private string $category
    ) {
        parent::__construct();
    }
}