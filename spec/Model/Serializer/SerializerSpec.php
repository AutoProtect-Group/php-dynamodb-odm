<?php

namespace spec\Autoprotect\DynamodbODM\Model\Serializer;

use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Annotation\Types\HashMapType;
use Autoprotect\DynamodbODM\Annotation\Types\ModelType;
use Autoprotect\DynamodbODM\Model\Exception\EmptyModelPropertyException;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Model\Serializer\Serializer;
use Autoprotect\DynamodbODM\Model\Serializer\SerializerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use Money\Currency;
use Money\Money;
use spec\Autoprotect\DynamodbODM\Model\EncryptorTrait;
use spec\Autoprotect\DynamodbODM\Model\fixtures\EncryptionModel;
use spec\Autoprotect\DynamodbODM\Model\fixtures\EncryptionModelWithScalarCollection;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\CustomerType;
use spec\Autoprotect\DynamodbODM\Model\fixtures\enums\OrderStatus;
use spec\Autoprotect\DynamodbODM\Model\fixtures\ModelWithEnumeration;
use spec\Autoprotect\DynamodbODM\Model\fixtures\ModelWithIsGetter;
use spec\Autoprotect\DynamodbODM\Model\fixtures\SortKeyModel;
use spec\Autoprotect\DynamodbODM\Model\fixtures\TestAsset;
use spec\Autoprotect\DynamodbODM\Model\fixtures\RelatedModel;
use spec\Autoprotect\DynamodbODM\Model\fixtures\SubRelatedModel;
use spec\Autoprotect\DynamodbODM\Model\fixtures\NewModel;
use PhpSpec\ObjectBehavior;
use TypeError;

class SerializerSpec extends ObjectBehavior
{
    use EncryptorTrait;

    private const FIXTURE_PRIMARY_KEY = 'id';
    private const FIXTURE_SORT_KEY = 'clientId';

    private const FIXTURE_DATA_MODEL = [
        'id' => 'fgdfgdfg213dsa',
        'name' => 'Test name',
        'price' => 12335,
        'priceNet' => [
            'amount' => '248',
            'currency' => 'GBP'
        ],
        'percent' => 34.7,
        'itemsAmount' => 3,
        'createdAt' => '2003-10-01T15:56:00+03:00',
        'isDeleted' => false,
        'isPhoneNumber' => true,
        'asset' => [
            'id' => 'test_asset_a435345',
            'engineType' => 'petrol',
            'type' => 'test',
            'mark' => 'BMW',
            'code' => '0',
            'model' => '3er',
        ],
        'buyer' => [
            'id' => 'asdasd',
            'name' => 'Test name 1',
            'applicant' => [
                'id' => 'a213a',
                'name' => 'test name 1',
            ],
        ],
        'buyers' => [
            [
                'id' => 'asdasd',
                'name' => 'Test name 1',
                'applicant' => [
                    'id' => 'a213a',
                    'name' => 'test name 1',
                ],
            ],
            [
                'id' => 'asdasd',
                'name' => 'Test name 1',
                'applicant' => [
                    'id' => 'a213a',
                    'name' => 'test name 1',
                ],
            ],
        ],
        'buyersMap' => [
                  "asdasd" => [
                        "id" => "asdasd",
                        "name" => "Test name 1",
                        "applicant" => [
                              "id" => "a213a",
                              "name" => "test name 1",
                            ],
                      ],
            ],
    ];
    private const FIXTURE_DATA_ARRAY = [
        self::FIXTURE_DATA_MODEL,
        self::FIXTURE_DATA_MODEL,
    ];

    public function let(
        AnnotationManagerInterface $annotationManager,

        PropertyInterface $newModelPrimaryKey,

        PropertyInterface $newModelSortKey,

        PropertyInterface $newModelName,
        PropertyInterface $newModelMoney,
        PropertyInterface $newModelMoneyObject,
        PropertyInterface $newModelFloat,
        PropertyInterface $newModelInteger,
        PropertyInterface $newModelDateTime,
        PropertyInterface $newModelBooleanIsDeleted,
        PropertyInterface $newModelBooleanIsPhoneNumber,
        PropertyInterface $newModelAsset,
        PropertyInterface $newModelBuyer,
        PropertyInterface $newModelBuyers,
        PropertyInterface $newModelBuyersMap,
        HashMapType $newModelBuyersMapAnnotationType,
        ModelType $newModelBuyerAnnotationType,
        ModelType $newModelAssetAnnotationType,

        PropertyInterface $relatedModelName,
        PropertyInterface $relatedModelApplicant,

        PropertyInterface $subRelatedModelName,

        PropertyInterface $assetModelEngineType,
        PropertyInterface $assetModelType,
        PropertyInterface $assetModelMark,
        PropertyInterface $assetModelCode,
        PropertyInterface $assetModelModelName,
    )
    {
        $newModelPrimaryKey->isPrimary()->willReturn(true);
        $newModelPrimaryKey->isSortKey()->willReturn(false);
        $newModelPrimaryKey->getName()->willReturn(static::FIXTURE_PRIMARY_KEY);
        $newModelPrimaryKey->getType()->willReturn('string');
        $newModelPrimaryKey->isEncrypted()->willReturn(false);

        $newModelSortKey->isPrimary()->willReturn(false);
        $newModelSortKey->isSortKey()->willReturn(true);
        $newModelSortKey->getName()->willReturn(static::FIXTURE_SORT_KEY);
        $newModelSortKey->getType()->willReturn('string');
        $newModelSortKey->isEncrypted()->willReturn(false);

        // main new model mockery
        {
            $newModelName->getName()->willReturn('name');
            $newModelName->getType()->willReturn('string');
            $newModelName->isEncrypted()->willReturn(false);

            $newModelMoney->getName()->willReturn('price');
            $newModelMoney->getType()->willReturn('money');
            $newModelMoney->isEncrypted()->willReturn(false);

            $newModelMoneyObject->getName()->willReturn('priceNet');
            $newModelMoneyObject->getType()->willReturn('moneyObject');
            $newModelMoneyObject->isEncrypted()->willReturn(false);

            $newModelFloat->getName()->willReturn('percent');
            $newModelFloat->getType()->willReturn('float');
            $newModelFloat->isEncrypted()->willReturn(false);

            $newModelInteger->getName()->willReturn('itemsAmount');
            $newModelInteger->getType()->willReturn('integer');
            $newModelInteger->isEncrypted()->willReturn(false);

            $newModelDateTime->getName()->willReturn('createdAt');
            $newModelDateTime->getType()->willReturn('datetime');
            $newModelDateTime->isEncrypted()->willReturn(false);

            $newModelBooleanIsDeleted->getName()->willReturn('isDeleted');
            $newModelBooleanIsDeleted->getType()->willReturn('boolean');
            $newModelBooleanIsDeleted->isEncrypted()->willReturn(false);

            $newModelBooleanIsPhoneNumber->getName()->willReturn('isPhoneNumber');
            $newModelBooleanIsPhoneNumber->getType()->willReturn('boolean');
            $newModelBooleanIsPhoneNumber->isEncrypted()->willReturn(false);

            $newModelAsset->getName()->willReturn('asset');
            $newModelAssetAnnotationType->getModelClassName()->willReturn(TestAsset::class);
            $newModelAsset->getTypeAnnotation()->willReturn($newModelBuyerAnnotationType);
            $newModelAsset->getType()->willReturn('model');
            $newModelAsset->isEncrypted()->willReturn(false);

            $newModelBuyer->getName()->willReturn('buyer');
            $newModelBuyerAnnotationType->getModelClassName()->willReturn(RelatedModel::class);
            $newModelBuyer->getTypeAnnotation()->willReturn($newModelBuyerAnnotationType);
            $newModelBuyer->getType()->willReturn('model');
            $newModelBuyer->isEncrypted()->willReturn(false);

            $newModelBuyers->getName()->willReturn('buyers');
            $newModelBuyers->getType()->willReturn('collection');
            $newModelBuyers->isEncrypted()->willReturn(false);

            $newModelBuyersMap->getName()->willReturn('buyersMap');
            $newModelBuyersMap->getType()->willReturn(HashMapType::TYPE_NAME);
            $newModelBuyersMapAnnotationType->getModelClassName()->willReturn(RelatedModel::class);
            $newModelBuyersMap->getTypeAnnotation()->willReturn($newModelBuyersMapAnnotationType);
            $newModelBuyersMap->isEncrypted()->willReturn(false);

            $newModelAnnotationData = [
                'id' => $newModelPrimaryKey,
                'name' => $newModelName,
                'price' => $newModelMoney,
                'priceNet' => $newModelMoneyObject,
                'percent' => $newModelFloat,
                'itemsAmount' => $newModelInteger,
                'createdAt' => $newModelDateTime,
                'isDeleted' => $newModelBooleanIsDeleted,
                'isPhoneNumber' => $newModelBooleanIsPhoneNumber,
                'asset' => $newModelAsset,
                'buyer' => $newModelBuyer,
                'buyers' => $newModelBuyers,
                'buyersMap' => $newModelBuyersMap
            ];
        }

        // related model mockery
        {
            $relatedModelName->getName()->willReturn('name');
            $relatedModelName->getType()->willReturn('string');
            $relatedModelName->isEncrypted()->willReturn(false);

            $relatedModelApplicant->getName()->willReturn('applicant');
            $relatedModelApplicant->getType()->willReturn('model');
            $relatedModelApplicant->isEncrypted()->willReturn(false);

            $relatedModelAnnotationData = [
                'id' => $newModelPrimaryKey,
                'name' => $relatedModelName,
                'applicant' => $relatedModelApplicant,
            ];
        }

        // sub related model mockery
        {
            $subRelatedModelName->getName()->willReturn('name');
            $subRelatedModelName->getType()->willReturn('string');
            $subRelatedModelName->isEncrypted()->willReturn(false);

            $subRelatedModelAnnotationData = [
                'id' => $newModelPrimaryKey,
                'name' => $subRelatedModelName,
            ];
        }

        // asset model mockery
        {
            $assetModelEngineType->getName()->willReturn('engineType');
            $assetModelEngineType->getType()->willReturn('string');
            $assetModelEngineType->isEncrypted()->willReturn(false);

            $assetModelType->getName()->willReturn('type');
            $assetModelType->getType()->willReturn('string');
            $assetModelType->isEncrypted()->willReturn(false);

            $assetModelMark->getName()->willReturn('mark');
            $assetModelMark->getType()->willReturn('string');
            $assetModelMark->isEncrypted()->willReturn(false);

            $assetModelCode->getName()->willReturn('code');
            $assetModelCode->getType()->willReturn('string');
            $assetModelCode->isEncrypted()->willReturn(false);

            $assetModelModelName->getName()->willReturn('model');
            $assetModelModelName->getType()->willReturn('string');
            $assetModelModelName->isEncrypted()->willReturn(false);

            $assetModelAnnotationData = [
                'id' => $newModelPrimaryKey,
                'engineType' => $assetModelEngineType,
                'type' => $assetModelType,
                'mark' => $assetModelMark,
                'code' => $assetModelCode,
                'model' => $assetModelModelName,
            ];
        }

        $annotationManager
            ->getPrimaryKeyByModelClassName(NewModel::class)
            ->willReturn('id');

        $annotationManager
            ->getPrimaryKeyByModelClassName(RelatedModel::class)
            ->willReturn('id');

        $annotationManager
            ->getPrimaryKeyByModelClassName(SubRelatedModel::class)
            ->willReturn('id');

        $annotationManager
            ->getPrimaryKeyByModelClassName(TestAsset::class)
            ->willReturn('id');

        $annotationManager
            ->getFieldTypesByModelClassName(NewModel::class)
            ->willReturn($newModelAnnotationData);

        $annotationManager
            ->getFieldTypesByModelClassName(RelatedModel::class)
            ->willReturn($relatedModelAnnotationData);

        $annotationManager
            ->getFieldTypesByModelClassName(SubRelatedModel::class)
            ->willReturn($subRelatedModelAnnotationData);

        $annotationManager
            ->getFieldTypesByModelClassName(TestAsset::class)
            ->willReturn($assetModelAnnotationData);

        $annotationManager
            ->getSortKeyByModelClassName(SortKeyModel::class)
            ->willReturn($newModelSortKey);

        $this->beConstructedWith($annotationManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Serializer::class);
    }

    public function it_implements_serializer_interface()
    {
        $this->shouldImplement(SerializerInterface::class);
    }

    public function it_serialize_the_model()
    {
        $model = $this->getTestModel();

        $this
            ->serialize($model)
            ->shouldBeArray()
        ;

        $this
            ->serialize($model)
            ->shouldReturn(self::FIXTURE_DATA_MODEL)
        ;
    }

    public function it_serializes_the_collection()
    {
        $model = $this->getTestModel();

        $this
            ->serialize($model)
            ->shouldBeArray();

        $this
            ->serialize([$model, $model])
            ->shouldReturn(self::FIXTURE_DATA_ARRAY);
    }

    public function it_can_serialize_the_hash_map(): void
    {
        $bauer = $this->getTestModel()->getBuyer();

        $this
            ->serialize([
                self::FIXTURE_DATA_MODEL['buyer']['id'] => $bauer
            ])
            ->shouldReturn([
                self::FIXTURE_DATA_MODEL['buyer']['id'] => self::FIXTURE_DATA_MODEL['buyer']
            ]);
    }

    public function it_can_encrypt_the_field(): void
    {
        $encryptor = $this->getEncryptor();

        $this->beConstructedWith(
            new AnnotationManager(
                new DualReader(
                    new AnnotationReader(),
                    new AttributeReader()
                )
            ),
            $encryptor,
        );

        $encryptedModel = new EncryptionModel();

        $encryptedModel->setId('someId');
        $encryptedModel->setEncryptedName('secretName');

        $this->serialize($encryptedModel)
            ->offsetGet("encryptedName")
            ->shouldNotBe("secretName");

        $serialized = $this->serialize($encryptedModel)
            ->offsetGet("encryptedName")->getWrappedObject();

        assert($encryptor->decrypt($serialized) === 'secretName');
    }

    public function it_can_encrypt_the_scalar_array_field(): void
    {
        $encryptor = $this->getScalarArrayEncryptor();

        $this->beConstructedWith(
            new AnnotationManager(
                new DualReader(
                    new AnnotationReader(),
                    new AttributeReader()
                )
            ),
            $encryptor,
        );

        $encryptedModel = (new EncryptionModelWithScalarCollection())
            ->setId('someId')
            ->setEncryptedName('secretName')
            ->setEncryptedArray([
                'data' => [
                    'object' => [
                        'secretProperty' => 'whisperSecret',
                        'someProperty' => 'someOtherProperty',
                    ]
                ]
            ])
        ;

        $this->serialize($encryptedModel)
            ->offsetGet("encryptedArray")
            ->offsetGet('data')
            ->offsetGet('object')
            ->offsetGet('secretProperty')
            ->shouldNotBe('whisperSecret')
        ;

        $serialized = $this->serialize($encryptedModel)
            ->offsetGet("encryptedArray")->getWrappedObject();

        assert($encryptor->decrypt(
            $serialized,
            ["encryptedProperty" => "secretProperty"]) === ['data' => ['object' => ['secretProperty' => 'whisperSecret', 'someProperty' => 'someOtherProperty']]]);
    }

    function it_can_read_is_boolean_properties(): void
    {
        $this->beConstructedWith(
            new AnnotationManager(
                new DualReader(
                    new AnnotationReader(),
                    new AttributeReader()
                )
            )
        );

        $testModel = new ModelWithIsGetter();

        $testModel
            ->setId('someId')
            ->setName('fancyName')
            ->setSendEmails(true)
        ;

        $this->serialize($testModel)
            ->offsetGet("sendEmails")
            ->shouldBe(true);
    }

    function it_can_serialize_enumerations()
    {
        $this->beConstructedWith(new AnnotationManager(new AttributeReader()));

        $testModel = new ModelWithEnumeration();

        $testModel->setId('someId');
        $testModel->setCustomerType(CustomerType::BUSINESS);
        $testModel->setOrderStatus(OrderStatus::IN_REVIEW);
        $testModel->setOrderStatusAdditional(null);

        $this->serialize($testModel)
            ->shouldBe(
                [
                    'id' => "someId",
                    'orderStatus' => "in-review",
                    'orderStatusAdditional' => null,
                    'customerType' => "BUSINESS",
                ]
            );
    }

    /**
     * @return NewModel
     *
     * @throws \Exception
     */
    private function getTestModel(): NewModel
    {
        $model = new NewModel();
        $buyer = new RelatedModel();
        $applicant = new SubRelatedModel();
        $asset = new TestAsset();

        $asset->setId('test_asset_a435345')
            ->setType('test')
            ->setEngineType('petrol')
            ->setMark('BMW')
            ->setCode('0')
            ->setModel('3er');

        $applicant->setId(self::FIXTURE_DATA_MODEL['buyer']['applicant']['id'])
            ->setName(self::FIXTURE_DATA_MODEL['buyer']['applicant']['name']);

        $buyer->setId(self::FIXTURE_DATA_MODEL['buyer']['id'])
            ->setName(self::FIXTURE_DATA_MODEL['buyer']['name'])
            ->setApplicant($applicant);

        $model->setId(self::FIXTURE_DATA_MODEL['id'])
            ->setName(self::FIXTURE_DATA_MODEL['name'])
            ->setPrice(123.345)
            ->setPriceNet(
                new Money(
                    self::FIXTURE_DATA_MODEL['priceNet']['amount'],
                    new Currency(self::FIXTURE_DATA_MODEL['priceNet']['currency'])
                )
            )
            ->setPercent(self::FIXTURE_DATA_MODEL['percent'])
            ->setItemsAmount(self::FIXTURE_DATA_MODEL['itemsAmount'])
            ->setCreatedAt(new \DateTime(self::FIXTURE_DATA_MODEL['createdAt']))
            ->setIsDeleted(self::FIXTURE_DATA_MODEL['isDeleted'])
            ->setIsPhoneNumber(self::FIXTURE_DATA_MODEL['isPhoneNumber'])
            ->setAsset($asset)
            ->setBuyer($buyer)
            ->setBuyers([$buyer, $buyer])
            ->setBuyersMap([$buyer->getId() => $buyer]);

        return $model;
    }

    public function it_should_throw_invalid_param_exception()
    {
        $notValidModel = (new class{});

        $this
            ->shouldThrow(TypeError::class)
            ->during('serialize', [$notValidModel]);
    }

    public function it_should_throw_empty_model_property_exception(
        AnnotationManagerInterface $annotationManager
    ) {
        $notValidModel = (new class implements ModelInterface {

            /**
             * @var string
             *
             * @Key\Primary
             */
            protected $id;

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
             * @return string
             */
            public function getId(): string
            {
                return $this->id;
            }

            /**
             * @return string
             */
            public static function getTableName():string
            {
                return 'NotValidTable';
            }
        });

        $model = (new $notValidModel())
            ->setId('1');

        $annotationManager
            ->getPrimaryKeyByModelClassName(get_class($notValidModel))
            ->willReturn(self::FIXTURE_PRIMARY_KEY);

        $annotationManager
            ->getFieldTypesByModelClassName(get_class($notValidModel))
            ->willReturn([]);

        $annotationManager
            ->getSortKeyByModelClassName(get_class($notValidModel))
            ->willReturn('');

        $this
            ->shouldThrow(EmptyModelPropertyException::class)
            ->during('serialize', [$model]);
    }
}
