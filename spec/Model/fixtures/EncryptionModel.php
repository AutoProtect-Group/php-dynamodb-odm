<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;
use Autoprotect\DynamodbODM\Annotation\Encryption\Encrypted;

class EncryptionModel extends Model
{
    #[Key\Primary, Types\StringType]
    protected string $id;

    #[Types\StringType, Encrypted]
    protected string $encryptedName;

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
    public function getEncryptedName(): string
    {
        return $this->encryptedName;
    }

    /**
     * @param string $name
     *
     * @return SubRelatedModel
     */
    public function setEncryptedName(string $name): self
    {
        $this->encryptedName = $name;

        return $this;
    }
}
