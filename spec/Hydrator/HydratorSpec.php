<?php

namespace spec\Autoprotect\DynamodbODM\Hydrator;

use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date\DateTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money\MoneyTypeHydrator;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money\MoneyTypeHydratorInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use PHPUnit\Framework\TestCase;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpSpec\ObjectBehavior;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\DefaultAsset;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\EncryptionModel;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\EncryptionModelWithScalarCollection;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\enums\ApplicationStatus;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\enums\CustomerType;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\enums\OrderStatus;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\ModelWithEnumeration;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\NewModel;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\RelatedModel;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\SubRelatedModel;
use spec\Autoprotect\DynamodbODM\Hydrator\fixtures\TestAsset;
use spec\Autoprotect\DynamodbODM\Model\EncryptorTrait;
use ValueError;

/**
 * Class HydratorSpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Hydrator
 */
class HydratorSpec extends ObjectBehavior
{
    use EncryptorTrait;

    protected const FIXTURE_DATA = [
        'id' => 'hk7364jdf7234j',
        'name' => 'new Name',
        'description' => 'Description of the product.',
        'priceNet' => [
            'amount' => '248',
            'currency' => 'GBP',
        ],
        'percent' => 34.7,
        'itemsAmount' => 3,
        'createdAt' => '2003-10-01T15:56:00+03:00',
        'isDeleted' => false,
        'isPhoneNumber' => true,
        'asset' => [
            'id' => 'asset_3321342',
            'type' => 'test',
            'mark' => 'BMW',
            'model' => '3er',
            'engineType' => 'petrol',
        ],
        'buyersMap' => [
            'buyerMapId1' => [
                'id' => 'buyerMapId1',
                'name' => 'Bayer Name',
                'applicant' => [
                    'id' => 'buyersMapIdApplicantId1',
                    'name' => 'applicant bayer name',
                ],
            ],
            'buyerMapId2' => [
                'id' => 'buyerMapId2',
                'name' => 'Bayer Name',
                'applicant' => [
                    'id' => 'buyersMapIdApplicantId2',
                    'name' => 'applicant bayer name',
                ],
            ]
        ],
        'buyer' => [
            'id' => 'fdsfsd23asd',
            'name' => 'new Name',
            'applicant' => [
                'id' => 'as213dasasd',
                'name' => 'test name',
            ],
        ],
        'buyers' => [
            [
                'id' => 'sdaasd',
                'name' => 'new Name',
                'applicant' => [
                    'id' => 'as213dasasd',
                    'name' => 'test name',
                ],
            ],
            [
                'id' => 'sdaasd1',
                'name' => 'new Name1',
                'applicant' => [
                    'id' => 'as213dasasd1',
                    'name' => 'test name1',
                ],
            ],
        ]
    ];

    protected const FIXTURE_ENCRYPTED_MODEL = [
        'id' => 'someId',
        'encryptedName' => 'def502000a8cd34f80934cb32bc78e8573db521180abd723fd3c72b007a369c980b7057df31c9c240793b350e0e989ea3c716843eb5fbf8fa9d928e951db42a594537a3663011cf404dac94a3ec581a1a2d7347afe94d9d3545fb9f3973b0b',
    ];

    protected const FIXTURE_ENCRYPTED_SCALAR_ARRAY_MODEL = [
        'id' => 'someId',
        'encryptedName' => 'def502000a8cd34f80934cb32bc78e8573db521180abd723fd3c72b007a369c980b7057df31c9c240793b350e0e989ea3c716843eb5fbf8fa9d928e951db42a594537a3663011cf404dac94a3ec581a1a2d7347afe94d9d3545fb9f3973b0b',
        'encryptedArray' => [
            'data' =>
                [
                    'object' =>
                        [
                            'secretProperty' => 'def50200061803eadcc3c861643385522597e4a6ff1c7cec2efe62aa7c24719941e2522e03ff9cb7b5b09a01011aa9f168f9d6ba428ccb97bc0e4ad0e669069c7dd092e2f44fa1d8c9922a0e5074e7d0d90804c9f2c8b486716b5a4ec122e60222',
                            'someProperty' => 'someOtherProperty',
                        ],
                ],
        ]
    ];

    protected const FIXTURE_ENUMERATIONS_MODEL = [
        'id' => "someId",
        'orderStatus' => "in-review",
        'unionStatus' => "NEW",
        'unionNullableStatus' => "INVALID",
        'orderStatusAdditional' => null,
        'customerType' => "BUSINESS",
    ];

    public function let(): void
    {
        $this->beConstructedWith(NewModel::class, new AnnotationManager());
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Hydrator::class);
    }

    public function it_should_hydrate_the_model(): void
    {
        $this->hydrate(static::FIXTURE_DATA)
            ->shouldBeAnInstanceOf(ModelInterface::class);
    }

    public function it_should_hydrate_model_and_its_fields(): void
    {
        $this->hydrate(static::FIXTURE_DATA)
            ->getId()
            ->shouldReturn('hk7364jdf7234j');

        $this->hydrate(array_merge(static::FIXTURE_DATA, ['name' => 'new Name 2']))
            ->getName()
            ->shouldReturn('new Name 2');

        $this->hydrate(static::FIXTURE_DATA)
            ->getPriceNet()
            ->getAmount()
            ->shouldReturn("248");

        $this->hydrate(static::FIXTURE_DATA)
            ->getPriceNet()
            ->getCurrency()
            ->getCode()
            ->shouldReturn("GBP");

        $this->hydrate(static::FIXTURE_DATA)
            ->getPriceNet()
            ->jsonSerialize()
            ->shouldReturn(static::FIXTURE_DATA['priceNet']);

        $this->hydrate(array_merge(static::FIXTURE_DATA, ['itemsAmount' => 4]))
            ->getItemsAmount()
            ->shouldReturn(4);

        $this->hydrate(array_merge(static::FIXTURE_DATA, ['createdAt' => '2019-09-10 14:35:12']))
            ->getCreatedAt()
            ->shouldBeAnInstanceOf(\DateTime::class);

        $this->hydrate(static::FIXTURE_DATA)
            ->getIsDeleted()
            ->shouldReturn(false);

        $this->hydrate(static::FIXTURE_DATA)
            ->getIsPhoneNumber()
            ->shouldReturn(true);

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->shouldBeAnInstanceOf(RelatedModel::class);

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->getId()
            ->shouldReturn('fdsfsd23asd');

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->getName()
            ->shouldReturn('new Name');

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->getApplicant()
            ->shouldBeAnInstanceOf(SubRelatedModel::class);

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->getApplicant()
            ->getId()
            ->shouldReturn('as213dasasd');

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyer()
            ->getApplicant()
            ->getName()
            ->shouldReturn('test name');

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyers()
            ->shouldBeLike([
                (new RelatedModel())
                    ->setId('sdaasd')
                    ->setName('new Name')
                    ->setApplicant(
                        (new SubRelatedModel())
                            ->setId('as213dasasd')
                            ->setName('test name')
                    )
                ,
                (new RelatedModel())
                    ->setId('sdaasd1')
                    ->setName('new Name1')
                    ->setApplicant(
                        (new SubRelatedModel())
                            ->setId('as213dasasd1')
                            ->setName('test name1')
                    )
            ]);

        $this->hydrate(static::FIXTURE_DATA)
            ->getBuyersMap()
            ->shouldBeLike(['buyerMapId1' =>
                (new RelatedModel())
                    ->setId('buyerMapId1')
                    ->setName('Bayer Name')
                    ->setApplicant(
                        (new SubRelatedModel())
                            ->setId('buyersMapIdApplicantId1')
                            ->setName('applicant bayer name')
                    )
                , 'buyerMapId2' => (new RelatedModel())
                    ->setId('buyerMapId2')
                    ->setName('Bayer Name')
                    ->setApplicant(
                        (new SubRelatedModel())
                            ->setId('buyersMapIdApplicantId2')
                            ->setName('applicant bayer name')
                    )
            ]);

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->shouldBeAnInstanceOf(TestAsset::class);

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->getId()
            ->shouldReturn('asset_3321342');

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->getType()
            ->shouldReturn('test');

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->getMark()
            ->shouldReturn('BMW');

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->getModel()
            ->shouldReturn('3er');

        $this->hydrate(static::FIXTURE_DATA)
            ->getAsset()
            ->getEngineType()
            ->shouldReturn('petrol');

        $buyer1 = $this->hydrate(static::FIXTURE_DATA)
            ->getBuyers()[0];

        $buyer2 = $this->hydrate(static::FIXTURE_DATA)
            ->getBuyers()[1];

        $buyer1
            ->getId()
            ->shouldReturn('sdaasd');

        $buyer1
            ->getName()
            ->shouldReturn('new Name');

        $buyer1
            ->getApplicant()
            ->shouldBeAnInstanceOf(SubRelatedModel::class);

        $buyer1
            ->getApplicant()
            ->getId()
            ->shouldReturn('as213dasasd');

        $buyer1
            ->getApplicant()
            ->getName()
            ->shouldReturn('test name');

        $buyer2
            ->getId()
            ->shouldReturn('sdaasd1');

        $buyer2
            ->getName()
            ->shouldReturn('new Name1');

        $buyer2
            ->getApplicant()
            ->shouldBeAnInstanceOf(SubRelatedModel::class);

        $buyer2
            ->getApplicant()
            ->getId()
            ->shouldReturn('as213dasasd1');

        $buyer2
            ->getApplicant()
            ->getName()
            ->shouldReturn('test name1');
    }

    public function it_should_hydrate_invalid_date_with_expected_invalid_value(): void
    {
        $this->hydrate(array_merge(static::FIXTURE_DATA, ['createdAt' => '&*@#?.:']))
            ->getCreatedAt()
            ->getTimestamp()
            ->shouldReturn(DateTypeHydratorInterface::INVALID_TIMESTAMP);
    }

    public function it_should_use_default_discriminator_if_not_discriminator_field_provided_in_request(): void
    {
        $this->hydrate(array_merge(static::FIXTURE_DATA, ['asset' => []]))
            ->getAsset()
            ->shouldBeAnInstanceOf(DefaultAsset::class);
    }

    public function it_should_hydrate_invalid_money_with_expected_invalid_value(): void
    {
        $result = $this
            ->hydrate(array_merge(static::FIXTURE_DATA, ['priceNet' => ['amount' => '&*@#?.:', 'currency' => 'GBP']]))
            ->getWrappedObject();

        TestCase::assertEquals(
            MoneyTypeHydratorInterface::INVALID_MONEY_CURRENCY_CODE,
            $result->getPriceNet()->getCurrency()->getCode()
        );

        TestCase::assertEquals(
            MoneyTypeHydratorInterface::INVALID_MONEY_AMOUNT,
            $result->getPriceNet()->getAmount()
        );
    }

    public function it_should_hydrate_invalid_float_value_into_null(): void
    {
        $result = $this
            ->hydrate(array_merge(static::FIXTURE_DATA, ['percent' => '']))
            ->getWrappedObject();

        TestCase::assertNull(
            $result->getPercent()
        );
    }

    public function it_should_hydrate_invalid_int_value_into_null(): void
    {
        $result = $this
            ->hydrate(array_merge(static::FIXTURE_DATA, ['itemsAmount' => '']))
            ->getWrappedObject();

        TestCase::assertNull(
            $result->getItemsAmount()
        );
    }

    function it_should_hydrate_enumerations(): void
    {
        $this->beConstructedWith(
            ModelWithEnumeration::class,
            new AnnotationManager(new AttributeReader())
        );

        $hydrated = $this->hydrate(static::FIXTURE_ENUMERATIONS_MODEL);

        $hydrated
            ->getFromWrappedObject('orderStatus')
            ->shouldBe(OrderStatus::IN_REVIEW);

        $hydrated
            ->getFromWrappedObject('unionStatus')
            ->shouldBe(ApplicationStatus::NEW);

        $hydrated
            ->getFromWrappedObject('unionNullableStatus')
            ->shouldBeNull();

        $hydrated
            ->getFromWrappedObject('id')
            ->shouldBe('someId');

        $hydrated
            ->getFromWrappedObject('orderStatusAdditional')
            ->shouldBeNull();

        $hydrated
            ->getFromWrappedObject('customerType')
            ->shouldBe(CustomerType::BUSINESS);
    }

    function it_should_throw_value_error_on_invalid_enum_value(): void
    {
        $this->beConstructedWith(
            ModelWithEnumeration::class,
            new AnnotationManager(new AttributeReader())
        );

        $this::shouldThrow(ValueError::class)->during('hydrate', [
            [
                'id' => "someId",
                'orderStatus' => "in-review",
                'unionStatus' => "INVALID",
                'unionNullableStatus' => "in-review",
                'orderStatusAdditional' => null,
                'customerType' => "BUSINESS",
            ]
        ]);

        $this::shouldThrow(ValueError::class)->during('hydrate', [
            [
                'id' => "someId",
                'orderStatus' => "INVALID",
                'unionStatus' => "in-review",
                'unionNullableStatus' => "in-review",
                'orderStatusAdditional' => null,
                'customerType' => "BUSINESS",
            ]
        ]);
    }

    function it_should_hydrate_encrypted_fields(): void
    {
        $this->beConstructedWith(
            EncryptionModel::class,
            new AnnotationManager(new DualReader(
                new AnnotationReader(),
                new AttributeReader()
            )),
            $this->getEncryptor()
        );

        $this->hydrate(static::FIXTURE_ENCRYPTED_MODEL)
            ->getFromWrappedObject('encryptedName')
            ->shouldBe('secretValue')
        ;
    }

    function it_should_hydrate_encrypted_scalar_array_fields(): void
    {
        $this->beConstructedWith(
            EncryptionModelWithScalarCollection::class,
            new AnnotationManager(new DualReader(
                new AnnotationReader(),
                new AttributeReader()
            )),
            $this->getScalarArrayEncryptor()
        );

        $this->hydrate(static::FIXTURE_ENCRYPTED_SCALAR_ARRAY_MODEL)
            ->getFromWrappedObject("encryptedArray")
            ->offsetGet('data')
            ->offsetGet('object')
            ->offsetGet('secretProperty')
            ->shouldBe('whisperSecret')
        ;
    }
}
