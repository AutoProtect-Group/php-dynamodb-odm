<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Annotation\Key\Primary;
use Autoprotect\DynamodbODM\Annotation\Types\BooleanType;
use Autoprotect\DynamodbODM\Annotation\Types\StringType;
use Autoprotect\DynamodbODM\Model\Model;

class ModelWithIsGetter extends Model
{
    #[Primary, StringType]
    protected string $id;

    #[StringType]
    protected string $name;

    #[BooleanType]
    protected bool $sendEmails;

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
     * @return ModelWithIsGetter
     */
    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ModelWithIsGetter
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSendEmails(): bool
    {
        return $this->sendEmails;
    }

    /**
     * @param bool $sendEmails
     *
     * @return ModelWithIsGetter
     */
    public function setSendEmails(bool $sendEmails): static
    {
        $this->sendEmails = $sendEmails;
        return $this;
    }


}
