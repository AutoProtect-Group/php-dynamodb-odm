<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Money\Money;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\CustomerType;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\OrderStatus;

class NewModel extends Model
{
    protected const TABLE_NAME = 'test-table';

    /***
     * @var string
     *
     * @Key\Primary
     * @Types\StringType
     */
    protected string $id;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected string $name;

    /**
     * @var float
     *
     * @Types\FloatType
     */
    protected float $price;

    /**
     * @Types\Money
     */
    protected Money $priceNet;

    /**
     * @var float
     *
     * @Types\FloatType
     */
    protected float $percent;

    /**
     * @var integer
     *
     * @Types\IntegerType
     */
    protected int $itemsAmount;

    /**
     * @var \DateTime
     *
     * @Types\DateType
     */
    protected \DateTime $createdAt;

    /**
     * @var bool
     *
     * @Types\BooleanType
     */
    protected bool $isDeleted;

    /**
     * @var bool
     *
     * @Types\BooleanType
     */
    protected bool $isPhoneNumber;

    /**
     * @var RelatedModel
     *
     * @Types\ModelType(modelClassName=RelatedModel::class)
     */
    protected RelatedModel $buyer;

    /**
     * @var array|RelatedModel[]
     *
     * @Types\CollectionType(modelClassName=RelatedModel::class)
     */
    protected array $buyers;

    /**
     * @var Asset
     *
     * @Types\ModelType(modelClassName=Asset::class)
     */
    protected Asset $asset;

    /**
     * @var array|RelatedModel[]
     *
     * @Types\HashMapType(modelClassName=RelatedModel::class)
     */
    protected array $buyersMap;

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
     * @return NewModel
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param \spec\Autoprotect\DynamodbODM\Model\fixtures\Asset $asset
     *
     * @return NewModel
     */
    public function setAsset(Asset $asset): NewModel
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * @return Asset|TestAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param RelatedModel $buyer
     *
     * @return NewModel
     */
    public function setBuyer(RelatedModel $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return RelatedModel
     */
    public function getBuyer(): RelatedModel
    {
        return $this->buyer;
    }

    /**
     * @param array|RelatedModel[] $buyers
     *
     * @return NewModel
     */
    public function setBuyers($buyers): self
    {
        $this->buyers = $buyers;

        return $this;
    }

    /**
     * @return array|RelatedModel[]
     */
    public function getBuyers(): array
    {
        return $this->buyers;
    }

    /**
     * @param array|RelatedModel[] $buyersMap
     *
     * @return NewModel
     */
    public function setBuyersMap($buyersMap): self
    {
        $this->buyersMap = $buyersMap;

        return $this;
    }

    /**
     * @return array|RelatedModel[]
     */
    public function getBuyersMap(): array
    {
        return $this->buyersMap;
    }

    /**
     * @param mixed $name
     *
     * @return NewModel
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param float $price
     *
     * @return NewModel
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param Money $priceNet
     *
     * @return NewModel
     */
    public function setPriceNet(Money $priceNet): self
    {
        $this->priceNet = $priceNet;

        return $this;
    }

    /**
     * @param float $percent
     *
     * @return NewModel
     */
    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @param int $itemsAmount
     *
     * @return NewModel
     */
    public function setItemsAmount(int $itemsAmount): self
    {
        $this->itemsAmount = $itemsAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return Money
     */
    public function getPriceNet(): Money
    {
        return $this->priceNet;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @return int
     */
    public function getItemsAmount(): int
    {
        return $this->itemsAmount;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return NewModel
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     *
     * @return NewModel
     */
    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPhoneNumber(): bool
    {
        return $this->isPhoneNumber;
    }

    /**
     * @param bool $isPhoneNumber
     *
     * @return NewModel
     */
    public function setIsPhoneNumber(bool $isPhoneNumber): self
    {
        $this->isPhoneNumber = $isPhoneNumber;

        return $this;
    }
}
