<?php

namespace spec\Autoprotect\DynamodbODM\Annotation\fixtures;

use DateTime;
use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Annotation\Key;
use Autoprotect\DynamodbODM\Annotation\Types;

class NewModel8Annotations extends Model
{
    #[Key\Primary]
    #[Types\StringType]
    protected string $idPhp8Attribute;

    #[Key\Sort]
    #[Types\StringType]
    protected string $php8SortKey;

    #[Types\IntegerType]
    protected int $intProperty;

    #[Types\BooleanType]
    protected bool $boolProperty;

    #[Types\DateType]
    protected DateTime $dateTimeProperty;

    #[Types\ModelType([Types\ModelType::MODEL_CLASS_NAME=>NewModel::class])]
    protected NewModel $injectedModel;

    public function __construct(
        #[Types\IntegerType]
        protected int $inlineConstructorProperty
    ){}
}