<?php

namespace spec\Autoprotect\DynamodbODM\Hydrator\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;
use Money\Money;

class NewModel extends Model
{
    /**
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
     * @Types\Money
     */
    protected Money $priceNet;

    /**
     * @var float|null
     *
     * @Types\FloatType
     */
    protected ?float $percent;

    /**
     * @var integer|null
     *
     * @Types\IntegerType
     */
    protected ?int $itemsAmount;

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
     * @var Asset
     *
     * @Types\ModelType(modelClassName=Asset::class)
     */
    protected ?Asset $asset;

    /**
     * @var RelatedModel
     *
     * @Types\ModelType(modelClassName=RelatedModel::class)
     */
    protected RelatedModel $buyer;

    /**
     * @var array|RelatedModel[]
     *
     * @Types\HashMapType(modelClassName=RelatedModel::class)
     */
    protected array $buyersMap;

    /**
     * @var array|RelatedModel[]
     *
     * @Types\CollectionType(modelClassName=RelatedModel::class)
     */
    protected array $buyers;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Asset $asset
     */
    public function setAsset(Asset $asset): void
    {
        $this->asset = $asset;
    }

    /**
     * @return Asset
     */
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    /**
     * @param RelatedModel $buyer
     */
    public function setBuyer(RelatedModel $buyer): void
    {
        $this->buyer = $buyer;
    }

    /**
     * @param array|RelatedModel[] $buyersMap
     */
    public function setBuyersMap($buyersMap): void
    {
        $this->buyersMap = $buyersMap;
    }

    /**
     * @return array|RelatedModel[]
     */
    public function getBuyersMap(): array
    {
        return $this->buyersMap;
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
     */
    public function setBuyers($buyers): void
    {
        $this->buyers = $buyers;
    }

    /**
     * @return array|RelatedModel[]
     */
    public function getBuyers(): array
    {
        return $this->buyers;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @param Money $priceNet
     */
    public function setPriceNet(Money $priceNet): void
    {
        $this->priceNet = $priceNet;
    }

    /**
     * @return Money
     */
    public function getPriceNet(): Money
    {
        return $this->priceNet;
    }

    /**
     * @param float|null $percent
     */
    public function setPercent(?float $percent): void
    {
        $this->percent = $percent;
    }

    /**
     * @param int|null $itemsAmount
     */
    public function setItemsAmount(?int $itemsAmount): void
    {
        $this->itemsAmount = $itemsAmount;
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
     * @return float|null
     */
    public function getPercent(): ?float
    {
        return $this->percent;
    }

    /**
     * @return int|null
     */
    public function getItemsAmount(): ?int
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
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
