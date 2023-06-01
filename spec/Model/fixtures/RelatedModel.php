<?php

namespace spec\Autoprotect\DynamodbODM\Model\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

class RelatedModel extends Model
{
    /**
     * @var string
     *
     * @Key\Primary
     * @Types\StringType
     */
    protected $id;

    /**
     * @var string
     *
     * @Types\StringType
     */
    protected $name;

    /**
     * @var SubRelatedModel
     *
     * @Types\ModelType(modelClassName=SubRelatedModel::class)
     */
    protected $applicant;

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
     * @return RelatedModel
     */
    public function setId(string $id): self
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
     * @return RelatedModel
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return SubRelatedModel
     */
    public function getApplicant(): SubRelatedModel
    {
        return $this->applicant;
    }

    /**
     * @param SubRelatedModel $applicant
     *
     * @return RelatedModel
     */
    public function setApplicant(SubRelatedModel $applicant): self
    {
        $this->applicant = $applicant;

        return $this;
    }
}