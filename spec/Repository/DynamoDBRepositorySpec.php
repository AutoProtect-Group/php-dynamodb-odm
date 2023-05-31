<?php

namespace spec\Autoprotect\DynamodbODM\Repository;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\Model;
use Autoprotect\DynamodbODM\Model\Serializer\SerializerInterface;
use Autoprotect\DynamodbODM\Query\OperationBuilder\DeleteItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GetItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\PutItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\ScanQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilderInterface;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;
use Autoprotect\DynamodbODM\Client\DealTrakDynamoClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DynamoDBRepositorySpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Repository
 */
class DynamoDBRepositorySpec extends ObjectBehavior
{
    private const SERIALIZED_ARRAY = [
        'id'            => 'qeeooogkgnnzg',
        'customerName'  => 'Fake customer name',
        'customerEmail' => 'Fake customer email',
        'percent'       => 99.99,
    ];

    public function let(
        Hydrator $hydrator,
        DealTrakDynamoClient $client,
        QueryBuilder $queryBuilder,
        Marshaler $marshaler,
        SerializerInterface $serializer
    ) {
        $annotationManager = new AnnotationManager();

        $hydrator->beConstructedWith([get_class($this->getModel()), $annotationManager, null]);
        $this->beConstructedWith(
            get_class($this->getModel()),
            $client,
            $queryBuilder,
            $hydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DynamoDBRepository::class);
    }

    public function it_saves_the_model(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        PutItemBuilder $putItem,
        SerializerInterface $serializer
    ) {
        $fakeModel = $this->getModel();
        $fakeSaveQuery = [
            'TableName' => $fakeModel::getTableName(),
            'Item'      => self::SERIALIZED_ARRAY,
        ];
        $fakeOutput = [
            'Item' => self::SERIALIZED_ARRAY,
        ];
        $fakeSerializedModel = [
            'id'            => 'qeeooogkgnnzg',
            'customerName'  => 'Fake customer name',
            'customerEmail' => 'Fake customer email',
            'percent'       => 99.99,
        ];

        $serializer
            ->serialize($fakeModel)
            ->willReturn($fakeSerializedModel);

        $queryBuilder
            ->putItem(Argument::any())
            ->shouldBeCalled()
            ->willReturn($putItem);

        $putItem
            ->itemData(self::SERIALIZED_ARRAY)
            ->shouldBeCalled()
            ->willReturn($putItem);

        $putItem
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeSaveQuery);

        $client
            ->put($fakeSaveQuery)
            ->shouldBeCalled()
            ->willReturn($fakeOutput);

        $this
            ->save($fakeModel)
            ->shouldReturn($fakeModel);
    }

    public function it_deletes_the_model(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        AnnotationManager $annotationManager,
        DeleteItemBuilder $deleteItem,
        PropertyInterface $idProperty,
        PropertyInterface $sortKeyProperty,
        SerializerInterface $serializer,
        Marshaler $marshaler,
        Hydrator $hydrator,
    ) {
        $fakeModel = $this->getModel();

        $hydrator->beConstructedWith([get_class($fakeModel), $annotationManager, null]);

        $this->beConstructedWith(
            get_class($this->getModel()),
            $client,
            $queryBuilder,
            $hydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );

        $fakeQueryDelete = [
            'TableName' => $fakeModel::getTableName(),
            'Key'       => [
                'id' => ['S' => $fakeModel->getId()],
            ],
        ];
        $fakeQueryDeleteOutput = ['DeletedItemId' => $fakeModel->getId()];

        $queryBuilder
            ->deleteItem($fakeModel::getTableName())
            ->shouldBeCalled()
            ->willReturn($deleteItem);

        $idProperty->getName()->willReturn('id');
        $idProperty->getType()->willReturn('string');
        $idProperty->isPrimary()->willReturn(true);
        $idProperty->isSortKey()->willReturn(false);

        $sortKeyProperty->getName()->willReturn('createdAt');
        $sortKeyProperty->getType()->willReturn('string');
        $sortKeyProperty->isPrimary()->willReturn(false);
        $sortKeyProperty->isSortKey()->willReturn(true);

        $annotationManager
            ->getPrimaryKeyByModelClassName(get_class($fakeModel))
            ->willReturn('id');

        $annotationManager
            ->getSortKeyByModelClassName(get_class($fakeModel))
            ->willReturn($sortKeyProperty);

        $deleteItem
            ->itemKey([
                'id' => $fakeModel->getId(),
            ])
            ->shouldBeCalled()
            ->willReturn($deleteItem);

        $deleteItem
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeQueryDelete);

        $client
            ->delete($fakeModel->getId(), $fakeQueryDelete)
            ->shouldBeCalled()
            ->willReturn($fakeQueryDeleteOutput);

        $this->delete($fakeModel)->shouldReturn($fakeModel);
    }

    public function it_gets_the_model(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        Hydrator $hydrator,
        Marshaler $marshaler,
        GetItemBuilder $getItem,
        AnnotationManager $annotationManager,
        SerializerInterface $serializer,
        PropertyInterface $idProperty,
    ) {
        $fakeModel = $this->getModel();

        $hydrator->beConstructedWith([get_class($fakeModel), $annotationManager, null]);

        $this->beConstructedWith(
            get_class($this->getModel()),
            $client,
            $queryBuilder,
            $hydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );

        $fakeGetItemQuery = [
            'TableName'      => $fakeModel::getTableName(),
            'ConsistentRead' => true,
            'Key'            => [
                'id' => ['S' => $fakeModel->getId()],
            ],
        ];
        $fakeGetItemQueryOutput = [
            'id' => ['S' => $fakeModel->getId()],
        ];

        $queryBuilder
            ->getItem($fakeModel::getTableName())
            ->shouldBeCalled()
            ->willReturn($getItem);

        $idProperty->getName()->willReturn('id');
        $idProperty->getType()->willReturn('string');
        $idProperty->isPrimary()->willReturn(true);
        $idProperty->isSortKey()->willReturn(false);

        $annotationManager
            ->getPrimaryKeyByModelClassName(get_class($fakeModel))
            ->willReturn('id');

        $getItem
            ->itemKey([
                'id' => $fakeModel->getId(),
            ])
            ->shouldBeCalled()
            ->willReturn($getItem);

        $getItem
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeGetItemQuery);

        $getItem
            ->setConsistentRead(Argument::exact(true))
            ->shouldBeCalled()
            ->willReturn($getItem);

        $client
            ->get($fakeModel->getId(), $fakeGetItemQuery)
            ->shouldBeCalled()
            ->willReturn($fakeGetItemQueryOutput);

        $unmarshaledItem = ['id' => $fakeModel->getId()];

        $marshaler
            ->unmarshalItem($fakeGetItemQueryOutput)
            ->willReturn($unmarshaledItem);

        $hydrator
            ->hydrate($unmarshaledItem)
            ->shouldBeCalled()
            ->willReturn($fakeModel);

        $this->get($fakeModel->getId())->shouldBe($fakeModel);
    }

    public function it_cannot_get_the_model(QueryBuilderInterface $criteria, DealTrakDynamoClient $client)
    {
        $fakeModel = $this->getModel();
        $fakeGetQuery = [
            'TableName' => $fakeModel::getTableName(),
        ];

        $criteria
            ->getQuery()
            ->willReturn($fakeGetQuery);

        $client
            ->scan($fakeGetQuery)
            ->willReturn(null);

        $this->shouldThrow(EntityNotFoundException::class);
    }

    public function it_gets_all_models(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        Hydrator $hydrator,
        ScanQueryBuilder $scan
    ) {
        $fakeModel = $this->getModel();
        $limit = null;
        $fakeScanQuery = [
            'TableName' => $fakeModel::getTableName(),
        ];
        $fakeScanOutput = [
            [
                'id'            => ['S' => 'testeltelte'],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 99.99],
            ],
            [
                'id'            => ['N' => 2],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 44.99],
            ],
            [
                'id'            => ['N' => 3],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 55.99],
            ],
        ];

        $queryBuilder
            ->scan($fakeModel::getTableName(), $limit)
            ->shouldBeCalled()
            ->willReturn($scan);

        $scan
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeScanQuery);

        $client
            ->scan($fakeScanQuery)
            ->shouldBeCalled()
            ->willReturn($fakeScanOutput);

        $hydrator
            ->hydrate(Argument::not(null))
            ->shouldBeCalled()
            ->willReturn($fakeModel);

        $this->getAll()->shouldReturn([
            $fakeModel,
            $fakeModel,
            $fakeModel,
        ]);
    }

    public function it_gets_few_models(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        Hydrator $hydrator,
        ScanQueryBuilder $scan
    ) {
        $fakeModel = $this->getModel();
        $limit = 3;
        $fakeScanQuery = [
            'TableName' => $fakeModel::getTableName(),
            'Limit' => $limit,
        ];
        $fakeScanOutput = [
            [
                'id'            => ['S' => 'testeltelte'],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 99.99],
            ],
            [
                'id'            => ['N' => 2],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 44.99],
            ],
            [
                'id'            => ['N' => 3],
                'customerName'  => ['S' => 'Fake customer name'],
                'customerEmail' => ['S' => 'Fake customer email'],
                'percent'       => ['N' => 58.99],
            ]
        ];

        $queryBuilder
            ->scan($fakeModel::getTableName(), $limit)
            ->shouldBeCalled()
            ->willReturn($scan);

        $scan
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeScanQuery);

        $client
            ->scan($fakeScanQuery)
            ->shouldBeCalled()
            ->willReturn($fakeScanOutput);

        $hydrator
            ->hydrate(Argument::not(null))
            ->shouldBeCalled()
            ->willReturn($fakeModel);

        $this
            ->setLimit($limit)
            ->getAll()
            ->shouldReturn([
            $fakeModel,
            $fakeModel,
            $fakeModel,
        ]);
    }

    public function it_can_update_the_model(
        QueryBuilder $queryBuilder,
        DealTrakDynamoClient $client,
        Hydrator $hydrator,
        UpdateItemBuilder $updateItem,
        AnnotationManager $annotationManager,
        Marshaler $marshaler,
        SerializerInterface $serializer,
        PropertyInterface $idProperty,
    ) {
        $fakeModel = $this->getModel();
        $hydrator->beConstructedWith([get_class($fakeModel), $annotationManager, null]);

        $this->beConstructedWith(
            get_class($this->getModel()),
            $client,
            $queryBuilder,
            $hydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );

        $fakeUpdateParams = [
            'id'           => 1,
            'customerName' => 'new fake customer name',
        ];
        $fakeUpdateQuery = [
            'TableName'        => $fakeModel::getTableName(),
            'Key'              => ['S' => $fakeModel->getId()],
            'AttributeUpdates' => $fakeUpdateParams,
        ];
        $fakeUpdateQueryOutput = [
            'UpdatedItemParams' => $fakeUpdateParams,
        ];

        $queryBuilder
            ->updateItem($fakeModel::getTableName())
            ->shouldBeCalled()
            ->willReturn($updateItem);

        $idProperty->getName()->willReturn('id');
        $idProperty->getType()->willReturn('string');
        $idProperty->isPrimary()->willReturn(true);
        $idProperty->isSortKey()->willReturn(false);

        $annotationManager
            ->getPrimaryKeyByModelClassName(get_class($fakeModel))
            ->willReturn('id');

        $updateItem
            ->itemKey([
                'id' => $fakeModel->getId(),
            ])
            ->shouldBeCalled()
            ->willReturn($updateItem);

        $updateItem
            ->attributes($fakeUpdateParams)
            ->shouldBeCalled()
            ->willReturn($updateItem);

        $updateItem
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($fakeUpdateQuery);

        $client
            ->update($fakeModel->getId(), $fakeUpdateQuery)
            ->shouldBeCalled()
            ->willReturn($fakeUpdateQueryOutput);

        $marshaler
            ->unmarshalItem($fakeUpdateQueryOutput)
            ->shouldBeCalled()
            ->willReturn($fakeUpdateQueryOutput);

        $hydrator
            ->hydrate($fakeUpdateQueryOutput)
            ->shouldBeCalled()
            ->willReturn($fakeModel);

        $this->update($fakeModel->getId(), $fakeUpdateParams)->shouldReturn($fakeModel);
    }

    protected function getModel(string $modelClassName = 'NewModel')
    {
        return new class($modelClassName) extends Model
        {
            public const TABLE_NAME = 'TestTable';

            public function getId(): ?string
            {
                return 'qeeooogkgnnzg';
            }
        };
    }
}
