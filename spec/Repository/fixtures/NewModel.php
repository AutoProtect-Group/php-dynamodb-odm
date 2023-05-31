<?php

namespace spec\Autoprotect\DynamodbODM\Repository\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

/**
 * Class NewModel
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Repository\fixtures
 */
final class NewModel extends Model
{
    public const TABLE_NAME = 'dynamo-db-test-table';

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
     */
    protected string $customerName;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $customerEmail;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $indexNameEmail;

    /**
     * @var float
     *
     * @Types\NumberType
     */
    protected float $percent;

    /**
     * @var array
     * @Types\HashMapType(modelClassName=Quotation::class)
     */
    protected ?array $quotations = null;

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

    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     *
     * @return self
     */
    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @param string $customerEmail
     *
     * @return self
     */
    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexNameEmail(): string
    {
        return implode('-', [$this->customerName, $this->customerEmail]);
    }

    /**
     * @param string $indexNameEmail
     * @return self
     */
    public function setIndexNameEmail(string $indexNameEmail): self
    {
        $this->indexNameEmail = $indexNameEmail;

        return $this;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     *
     * @return self
     */
    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getQuotations(): ?array
    {
        return $this->quotations;
    }

    public function setQuotations(?array $quotations): self
    {
        $this->quotations = $quotations;

        return $this;
    }
}
