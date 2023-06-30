<?php

declare(strict_types=1);


use Autoprotect\DynamodbODM\Annotation\Key\Primary;
use Autoprotect\DynamodbODM\Annotation\Types\BooleanType;
use Autoprotect\DynamodbODM\Annotation\Types\CollectionType;
use Autoprotect\DynamodbODM\Annotation\Types\DateType;
use Autoprotect\DynamodbODM\Annotation\Types\FloatType;
use Autoprotect\DynamodbODM\Annotation\Types\HashMapType;
use Autoprotect\DynamodbODM\Annotation\Types\IntegerType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelType;
use Autoprotect\DynamodbODM\Annotation\Types\Money;
use Autoprotect\DynamodbODM\Annotation\Types\StringType;
use Autoprotect\DynamodbODM\Model\Model;

class ExampleDemoModel extends Model
{
    protected const TABLE_NAME = 'test-table';

    #[StringType, Primary]  protected string $id;
    #[StringType]           protected string $name;
    #[FloatType]            protected float $price;
    #[Money]                protected Money $priceNet;
    #[FloatType]            protected float $percent;
    #[IntegerType]          protected int $itemsAmount;
    #[DateType]             protected DateTime $createdAt;
    #[BooleanType]          protected bool $isDeleted;
    #[BooleanType]          protected bool $isPhoneNumber;
    #[ModelType([ModelType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected RelatedModel $buyer;
    #[CollectionType([CollectionType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected array $buyers;
    #[ModelType([Asset::MODEL_CLASS_NAME => Asset::class])]
    protected Asset $asset;
    #[HashMapType([HashMapType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected array $buyersMap;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
    public function setAsset(Asset $asset): NewModel
    {
        $this->asset = $asset;

        return $this;
    }

    public function getAsset()
    {
        return $this->asset;
    }

    public function setBuyer(RelatedModel $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getBuyer(): RelatedModel
    {
        return $this->buyer;
    }

    public function setBuyers($buyers): self
    {
        $this->buyers = $buyers;

        return $this;
    }

    public function getBuyers(): array
    {
        return $this->buyers;
    }

    public function setBuyersMap($buyersMap): self
    {
        $this->buyersMap = $buyersMap;

        return $this;
    }

    public function getBuyersMap(): array
    {
        return $this->buyersMap;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setPriceNet(Money $priceNet): self
    {
        $this->priceNet = $priceNet;

        return $this;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function setItemsAmount(int $itemsAmount): self
    {
        $this->itemsAmount = $itemsAmount;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceNet(): Money
    {
        return $this->priceNet;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function getItemsAmount(): int
    {
        return $this->itemsAmount;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getIsPhoneNumber(): bool
    {
        return $this->isPhoneNumber;
    }
    public function setIsPhoneNumber(bool $isPhoneNumber): self
    {
        $this->isPhoneNumber = $isPhoneNumber;

        return $this;
    }
}
