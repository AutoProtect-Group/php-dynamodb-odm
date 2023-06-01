<?php

namespace spec\Autoprotect\DynamodbODM\Repository\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;

/**
 * Class Quotation
 *
 * @package spec\Autoprotect\DynamodbODM\Repository\fixtures
 */
final class Quotation extends Model
{
    public const TABLE_NAME = 'dynamo-db-test-table';

    /**
     * @var string
     *
     * @Key\Primary
     */
    protected string $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
