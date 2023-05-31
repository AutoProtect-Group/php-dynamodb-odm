<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;
use Autoprotect\DynamodbODM\Annotation\Encryption\Encrypted;

class EncryptionModelWithScalarCollection extends Model
{
    #[Key\Primary, Types\StringType]
    protected string $id;

    #[Types\ScalarCollectionType, Encrypted(["encryptedProperty" => "secretProperty"])]
    protected array $encryptedArray;

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
     * @return array
     */
    public function getEncryptedArray(): array
    {
        return $this->encryptedArray;
    }

    public function setEncryptedArray(array $encryptedArray): static
    {
        $this->encryptedArray = $encryptedArray;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedName(): string
    {
        return $this->encryptedName;
    }

    public function setEncryptedName(string $name): static
    {
        $this->encryptedName = $name;

        return $this;
    }
}
