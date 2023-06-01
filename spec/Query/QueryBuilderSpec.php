<?php

namespace spec\Autoprotect\DynamodbODM\Query;

use stdClass;
use Autoprotect\DynamodbODM\Query\QueryBuilderInterface;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GsiQueryBuilder;
use Autoprotect\DynamodbODM\Query\Expression\Condition\AttributeTypeIs;
use Autoprotect\DynamodbODM\Query\OperationBuilder\Transact\TransactUpdateQuery;
use Autoprotect\DynamodbODM\Query\OperationBuilder\QueryQueryBuilder;
use PhpSpec\ObjectBehavior;
use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\ScanQueryBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\BatchWriteItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\DeleteItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\PutItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GetItemBuilder;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use Autoprotect\DynamodbODM\Query\Expression\Condition\AttributeNotExistsExpression;

/**
 * Class QueryBuilderSpec
 *
 * @package spec\Autoprotect\DynamodbODM\Query
 */
class QueryBuilderSpec extends ObjectBehavior
{
    protected string $tableName = 'TestTable';
    protected string $indexName = 'TestIndex';

    protected ?int $limit = null;

    public function let(): void
    {
        $marshaller = new Marshaler();
        $this->beConstructedWith($marshaller, new ExpressionFactory($marshaller));
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(QueryBuilder::class);
    }

    public function it_can_build_base_scan_db_table_query(): void
    {
        $this
            ->scan($this->tableName, $this->limit)
            ->shouldReturnAnInstanceOf(ScanQueryBuilder::class);

        $query = $this
            ->scan($this->tableName, $this->limit)
            ->getQuery();

        $query->shouldHaveKeyWithValue('TableName', $this->tableName);
        $query->shouldHaveKeyWithValue('ConsistentRead', true);
    }

    public function it_can_build_base_scan_db_table_query_with_limit(): void
    {
        $this->limit = 3;

        $this
            ->scan($this->tableName, $this->limit)
            ->shouldReturnAnInstanceOf(ScanQueryBuilder::class);

        $query = $this
            ->scan($this->tableName, $this->limit)
            ->getQuery();

        $query->shouldHaveKeyWithValue('TableName', $this->tableName);
        $query->shouldHaveKeyWithValue('Limit', $this->limit);
    }

    public function it_can_build_scan_db_table_query_with_eq_expression(): void
    {
        $attributeNames = [
            '#id' => 'id',
            '#n' => 'name',
            '#e' => 'emails'
        ];

        $this->scan($this->tableName, $this->limit)->shouldReturnAnInstanceOf(ScanQueryBuilder::class);

        $query = $this
            ->scan($this->tableName, $this->limit)
            ->withAttributeNames($attributeNames)
            ->eq('#id', 1)
            ->getQuery();

        $query->shouldHaveKeyWithValue('TableName', $this->tableName);
        $query->shouldHaveKeyWithValue('ExpressionAttributeNames', $attributeNames);

        //TODO add 'FilterExpression' and 'ExpressionAttributeValues' check
    }

    public function it_can_build_batch_write_item_query(): void
    {
        $expectedQuery = [
            'RequestItems' => [
                $this->tableName => [
                    [
                        'DeleteRequest' => [
                            'Key' => [
                                'id' => [
                                    'N' => '1'
                                ]
                            ]
                        ]
                    ],
                    [
                        'PutRequest' => [
                            'Item' => [
                                'id' => [
                                    'N' => '2'
                                ],
                            ]
                        ]
                    ]
                ],
            ]
        ];

        $this->batchWriteItem()
            ->shouldReturnAnInstanceOf(BatchWriteItemBuilder::class);

        $this->batchWriteItem()
            ->delete($this->tableName, ['id' => 1])
            ->put($this->tableName, ['id' => 2])
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_get_item_query(): void
    {
        $primaryKey = ['id' => 1];

        $expectedGetItemQuery = [
            'TableName' => $this->tableName,
            'Key' => [
                'id' => [
                    'N' => '1'
                ]
            ],
            'ConsistentRead' => true,
        ];

        $expectedGetItemWithProjectionsQuery = array_merge($expectedGetItemQuery, [
            'ProjectionExpression' => "#e57e21836b0f, #c6f26059907d, #2148952c2c47.#c06bcd598a7b.#89f659627b39",
            'ExpressionAttributeNames' => [
                '#e57e21836b0f' => 'attribute1',
                '#c6f26059907d' => 'attribute[0]',
                '#2148952c2c47' => 'attr',
                '#c06bcd598a7b' => 'attr2',
                '#89f659627b39' => 'subAttr',
            ]
        ]);

        $this->getItem($this->tableName)
            ->shouldReturnAnInstanceOf(GetItemBuilder::class);

        $this->getItem($this->tableName)
            ->itemKey($primaryKey)
            ->setConsistentRead(true)
            ->getQuery()
            ->shouldBe($expectedGetItemQuery);
        
        $this->getItem($this->tableName)
            ->itemKey($primaryKey)
            ->setConsistentRead(true)
            ->setProjections(['attribute1', 'attribute[0]', 'attr.attr2.subAttr'])
            ->getQuery()
            ->shouldBe($expectedGetItemWithProjectionsQuery);
    }

    public function it_can_build_get_item_query_with_composite_key(): void
    {
        $primaryKey = [
            'id' => '1693608a-8f1e-4bf7-9ee7-2b8e45a7c14d',
            'createdAtMicroseconds' => 1622809171244
        ];

        $expectedGetItemQuery = [
            'TableName' => $this->tableName,
            'Key' => [
                'id' => [
                    'S' => '1693608a-8f1e-4bf7-9ee7-2b8e45a7c14d'
                ],
                'createdAtMicroseconds' => [
                    'N' => '1622809171244'
                ]
            ],
            'ConsistentRead' => true,
        ];

        $this->getItem($this->tableName)
            ->shouldReturnAnInstanceOf(GetItemBuilder::class);

        $this->getItem($this->tableName)
            ->itemKey($primaryKey)
            ->setConsistentRead(true)
            ->getQuery()
            ->shouldBe($expectedGetItemQuery);
    }

    public function it_can_build_update_item_query(): void
    {
        $primaryKey = ['id' => '1'];

        $newAttributeValues = [
            'name' => 'newName',
            'email' => 'newEmail@gamil.com'
        ];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'Key' => [
                'id' => [
                    'S' => '1'
                ]
            ],
            'ExpressionAttributeNames' => [
                '#82a3537ff0db' => 'name',
                '#82244417f956' => 'email',
            ],
            'ExpressionAttributeValues' => [
                ':2684112529c7' => [
                    'S' => 'newName',
                ],
                ':c6de2e0ed428' => [
                    'S' => 'newEmail@gamil.com',
                ],
            ],

            'ReturnValues' => UpdateItemBuilder::ALL_NEW,
            'UpdateExpression' => 'SET #82a3537ff0db = :2684112529c7, #82244417f956 = :c6de2e0ed428'
        ];

        $this->updateItem($this->tableName)
            ->shouldReturnAnInstanceOf(UpdateItemBuilder::class);

        $this->updateItem($this->tableName)
            ->itemKey($primaryKey)
            ->attributes($newAttributeValues)
            ->getQuery()
            ->shouldBeLike($expectedQuery);
    }

    public function it_can_build_update_item_with_list_append_query(): void
    {
        $primaryKey = ['id' => '1'];
        $marshaller = new Marshaler();
        $arrayData = [json_decode(file_get_contents(dirname(__DIR__)."/Query/Fixtures/mock_event_data.json"))];
        $newAttributeValues = ['events' => $arrayData];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'Key' => [
                'id' => [
                    'S' => '1'
                ]
            ],
            'ExpressionAttributeNames' => [
                '#862417b9e7c3' => 'events',
                '#82a3537ff0db' => 'name',
            ],
            'ExpressionAttributeValues' => [
                ':a3d8fd347c3f' => $marshaller->marshalValue($arrayData),
                ':2684112529c7' => ['S'=>'newName']
            ],

            'ReturnValues' => UpdateItemBuilder::NONE,
            'UpdateExpression' => 'SET #82a3537ff0db = :2684112529c7, #862417b9e7c3 = list_append(:a3d8fd347c3f, #862417b9e7c3)'
        ];

        $this->updateItem($this->tableName)
            ->shouldReturnAnInstanceOf(UpdateItemBuilder::class);

        $this->updateItem($this->tableName)
            ->itemKey($primaryKey)
            ->attributes([ 'name' => 'newName'])
            ->withReturnNone()
            ->attributesAppendList($newAttributeValues)
            ->getQuery()
            ->shouldBeLike($expectedQuery);
    }

    public function it_can_build_update_item_query_with_conditions(): void
    {
        $primaryKey = ['id' => '1'];

        $newAttributeValues = [
            'quotations' => new stdClass
        ];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#c2cefbb25293' => 'quotations',
                '#33b6cffc4940' => 'quotations',
            ],
            'ExpressionAttributeValues' => [
                ':a311c5cd67e4' => [
                    'M' => [],
                ],
                ':e44644631151' => [
                    'S' => "M",
                ],
            ],
            'Key' => [
                'id' => [
                    'S' => '1',
                ],
            ],
            'ReturnValues' => UpdateItemBuilder::ALL_NEW,
            'UpdateExpression' => 'SET #c2cefbb25293 = :a311c5cd67e4',
            'ConditionExpression' => '(attribute_not_exists(#c2cefbb25293) OR NOT attribute_type(#33b6cffc4940, :e44644631151))',
        ];

        $this->updateItem($this->tableName)
            ->shouldReturnAnInstanceOf(UpdateItemBuilder::class);

        $this->updateItem($this->tableName)
            ->itemKey($primaryKey)
            ->attributes($newAttributeValues)
            ->addKeyCondition('quotations', AttributeNotExistsExpression::class)
            ->addKeyValueCondition(
                'quotations',
                'M',
                AttributeTypeIs::class,
                QueryBuilderInterface::OPERATOR_OR . ' ' . QueryBuilderInterface::OPERATOR_NOT
            )
            ->getQuery()
            ->shouldBeLike($expectedQuery);
    }

    public function it_can_build_put_item_query(): void
    {
        $itemData = [
            'id' => 1,
            'approved' => true
        ];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'Item' => [
                'id' => [
                    'N' => '1'
                ],
                'approved' => [
                    'BOOL' => true
                ]
            ],
        ];

        $this->putItem($this->tableName)
            ->shouldReturnAnInstanceOf(PutItemBuilder::class);

        $this->putItem($this->tableName)
            ->itemData($itemData)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_delete_item_query(): void
    {
        $primaryKey = ['id' => 1];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'Key' => [
                'id' => [
                    'N' => '1'
                ]
            ]
        ];

        $this->deleteItem($this->tableName)
            ->shouldReturnAnInstanceOf(DeleteItemBuilder::class);

        $this->deleteItem($this->tableName)
            ->itemKey($primaryKey)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND sort = :73b811b63cf8)',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
            ],
            'ConsistentRead' => true,
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition($sortKeyName, $sortKeyValue)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_limits(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND sort = :73b811b63cf8)',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
            ],
            'ConsistentRead' => true,
            'Limit' => 10
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition($sortKeyName, $sortKeyValue)
            ->setLimit(10)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_begins_with_condition(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND begins_with(sort, :73b811b63cf8))',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
            ],
            'ConsistentRead' => true,
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition(
                $sortKeyName,
                $sortKeyValue,
                QueryBuilderInterface::OPERATOR_AND,
                QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH
            )
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_single_simplified_filter_condition(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND begins_with(sort, :73b811b63cf8))',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
                ':88a70cfbe112' => [
                    'N' => '5',
                ],
            ],
            'ConsistentRead' => true,
            'FilterExpression' => '(statusId = :88a70cfbe112)',
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition(
                $sortKeyName,
                $sortKeyValue,
                QueryBuilderInterface::OPERATOR_AND,
                QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH
            )
            ->addFilterConditions(['statusId' => 5], true)
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_single_filter_condition(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND begins_with(sort, :73b811b63cf8))',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
                ':88a70cfbe112' => [
                    'N' => '5',
                ],
            ],
            'ConsistentRead' => true,
            'FilterExpression' => '(statusId = :88a70cfbe112)',
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition(
                $sortKeyName,
                $sortKeyValue,
                QueryBuilderInterface::OPERATOR_AND,
                QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH
            )
            ->addFilterConditions([
                'statusId' => [
                    'value' => 5,
                    'operator' => QueryBuilderInterface::OPERATOR_OR
                ],

            ], true)
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_multiple_filter_conditions(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND begins_with(sort, :73b811b63cf8))',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
                ':88a70cfbe112' => [
                    'N' => '5',
                ],
                ':f6a9bf592b47' => [
                    'N' => '10',
                ],
            ],
            'ConsistentRead' => true,
            'FilterExpression' => '(statusId = :88a70cfbe112 OR statusId = :f6a9bf592b47)',
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition(
                $sortKeyName,
                $sortKeyValue,
                QueryBuilderInterface::OPERATOR_AND,
                QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH
            )
            ->addFilterConditions([
                'statusId' => [
                    'value' => [5, 10],
                    'operator' => QueryBuilderInterface::OPERATOR_OR
                ],

            ], true)
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_multi_field_multiple_filter_conditions(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND begins_with(sort, :73b811b63cf8))',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
                ':88a70cfbe112' => [
                    'N' => '5',
                ],
                ':f6a9bf592b47' => [
                    'N' => '10',
                ],
                ':e0672a8bbb66' => [
                    'N' => '15',
                ],
                ':5dd8a7d313d6' => [
                    'N' => '20',
                ],
            ],
            'ConsistentRead' => true,
            'FilterExpression' => '(statusId = :88a70cfbe112 OR statusId = :f6a9bf592b47 OR fieldId = :e0672a8bbb66 OR fieldId = :5dd8a7d313d6)',
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition(
                $sortKeyName,
                $sortKeyValue,
                QueryBuilderInterface::OPERATOR_AND,
                QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH
            )
            ->addFilterConditions([
                'statusId' => [
                    'value' => [5, 10],
                    'operator' => QueryBuilderInterface::OPERATOR_OR
                ],
                'fieldId' => [
                    'value' => [15, 20],
                    'operator' => QueryBuilderInterface::OPERATOR_OR
                ],

            ], true)
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_query_with_sort(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND sort = :73b811b63cf8)',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
            ],
            'ConsistentRead' => true,
            'ScanIndexForward' => false
        ];

        $this
            ->query($this->tableName)
            ->shouldReturnAnInstanceOf(QueryQueryBuilder::class);

        $this->query($this->tableName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition($sortKeyName, $sortKeyValue)
            ->setScanIndexForward(false)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_query_item_by_index_query(): void
    {
        $primaryKeyName = 'id';
        $primaryKeyValue = '1';
        $sortKeyName = 'sort';
        $sortKeyValue = '2';

        $expectedQuery = [
            'TableName' => $this->tableName,
            'KeyConditionExpression' => '(id = :90005db1e7f6 AND sort = :73b811b63cf8)',
            'ExpressionAttributeValues' => [
                ':90005db1e7f6' => [
                    'S' => '1',
                ],
                ':73b811b63cf8' => [
                    'S' => '2',
                ],
            ],
            'IndexName' => 'TestIndex',
        ];

        $this
            ->queryIndex($this->tableName, $this->indexName)
            ->shouldReturnAnInstanceOf(GsiQueryBuilder::class);

        $this->queryIndex($this->tableName, $this->indexName)
            ->addKeyCondition($primaryKeyName, $primaryKeyValue)
            ->addKeyCondition($sortKeyName, $sortKeyValue)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_build_put_item_query_with_conditions(): void
    {
        $primaryKeyName = 'referenceCode';
        $sortKeyName = 'clientId';

        $itemData = [
            'id' => 1,
            'approved' => true,
        ];

        $expectedQuery = [
            'TableName' => $this->tableName,
            'Item' => [
                'id' => [
                    'N' => '1',
                ],
                'approved' => [
                    'BOOL' => true,
                ]
            ],
            'ConditionExpression' => '(attribute_not_exists(' . $primaryKeyName . ') OR attribute_not_exists(' . $sortKeyName . '))',
        ];

        $this->putItem($this->tableName)
            ->shouldReturnAnInstanceOf(PutItemBuilder::class);

        $this->putItem($this->tableName)
            ->itemData($itemData)
            ->addCondition($primaryKeyName)
            ->addCondition($sortKeyName)
            ->getQuery()
            ->shouldBe($expectedQuery);
    }

    public function it_can_transact_write_item()
    {
        $fakeUpdateItemBuilder = new UpdateItemBuilder(new Marshaler(), $this->tableName);
        $fakeUpdateParams = [
            'name' => 'updated name',
            'email' => 'updatedemail@email.email',
        ];
        $fakeTransactionParams = new TransactUpdateQuery(
            $this->tableName,
            ['id' => 1],
            $fakeUpdateParams,
            $fakeUpdateItemBuilder
        );

        $expectedQuery = [
            'TransactItems' => [
                [
                    'Update' => [
                        'TableName' => 'TestTable',
                        'ExpressionAttributeNames' => [
                            '#82a3537ff0db' => 'name',
                            '#82244417f956' => 'email',
                        ],
                        'ExpressionAttributeValues' => [
                            ':2684112529c7' => [
                                'S' => 'updated name',
                            ],
                            ':c6de2e0ed428' => [
                                'S' => 'updatedemail@email.email'
                            ]
                        ],
                        'Key' => [
                            'id' => [
                                'N' => '1'
                            ]
                        ],
                        'ReturnValues' => 'ALL_NEW',
                        'UpdateExpression' => 'SET #82a3537ff0db = :2684112529c7, #82244417f956 = :c6de2e0ed428',
                    ]
                ]
            ]
        ];

        $this
            ->transactWriteItem()
            ->addTransaction($fakeTransactionParams)
            ->getQuery()
            ->shouldBeEqualTo($expectedQuery);
    }
}
