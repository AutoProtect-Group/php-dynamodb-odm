<?php

namespace spec\Autoprotect\DynamodbODM\Repository\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

final class SortKeyModel extends Model
{
    public const TABLE_NAME = 'dynamo-db-sort-key-test-table';

    /**
     * @var string
     *
     * @Types\StringType
     * @Key\Primary
     */
    protected string $id;

    /**
     * @var string
     *
     * @Types\StringType
     * @Key\Sort
     */
    protected string $clientId;

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

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
