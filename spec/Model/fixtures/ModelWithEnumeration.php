<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Annotation\Key\Primary;
use Autoprotect\DynamodbODM\Annotation\Types\EnumType;
use Autoprotect\DynamodbODM\Annotation\Types\StringType;
use Autoprotect\DynamodbODM\Model\Model;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\CustomerType;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\OrderStatus;

class ModelWithEnumeration extends Model
{
    #[Primary, StringType]
    protected string $id;

    #[EnumType]
    protected OrderStatus $orderStatus;

    #[EnumType(isStrict: false)]
    protected ?OrderStatus $orderStatusAdditional;

    #[EnumType]
    protected CustomerType $customerType;

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
     * @return ModelWithEnumeration
     */
    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return OrderStatus
     */
    public function getOrderStatus(): OrderStatus
    {
        return $this->orderStatus;
    }

    /**
     * @param OrderStatus $orderStatus
     *
     * @return ModelWithEnumeration
     */
    public function setOrderStatus(OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    /**
     * @return OrderStatus|null
     */
    public function getOrderStatusAdditional(): ?OrderStatus
    {
        return $this->orderStatusAdditional;
    }

    /**
     * @param OrderStatus|null $orderStatusAdditional
     *
     * @return ModelWithEnumeration
     */
    public function setOrderStatusAdditional(?OrderStatus $orderStatusAdditional): static
    {
        $this->orderStatusAdditional = $orderStatusAdditional;
        return $this;
    }

    /**
     * @return CustomerType
     */
    public function getCustomerType(): CustomerType
    {
        return $this->customerType;
    }

    /**
     * @param CustomerType $customerType
     *
     * @return ModelWithEnumeration
     */
    public function setCustomerType(CustomerType $customerType): static
    {
        $this->customerType = $customerType;
        return $this;
    }
}
