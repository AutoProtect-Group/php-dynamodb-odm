<?php


namespace spec\Autoprotect\DynamodbODM\Repository\fixtures;

use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

final class NewModelNested extends Model
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
    protected string $name;


    /**
     * @var NewModel
     *
     * @Types\ModelType(modelClassName=NewModel::class)
     */
    protected NewModel $childObject;

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @return NewModel
     */
    public function getChildObject(): NewModel
    {
        return $this->childObject;
    }

    /**
     * @param NewModel $childObject
     */
    public function setChildObject(NewModel $childObject): void
    {
        $this->childObject = $childObject;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


}
