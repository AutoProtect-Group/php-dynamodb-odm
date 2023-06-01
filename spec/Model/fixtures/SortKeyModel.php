<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;

abstract class SortKeyModel extends Model
{
    /**
     * @Key\Primary
     */
    protected string $referenceCode;

    /**
     * @Key\Sort
     */
    protected string $clientId;


    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(string $referenceCode): self
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }
}
