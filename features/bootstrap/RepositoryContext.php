<?php

declare(strict_types=1);

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Client\DealTrakDynamoClient;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\Serializer\Serializer;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilderInterface;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentOperation\GetDocument;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentRepository;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\ScalarOperation\GetProperty;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;
use Autoprotect\DynamodbODM\Repository\Exception\ConditionFailedException;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;
use Autoprotect\DynamodbODM\Repository\Exception\NothingFoundException;
use Autoprotect\DynamodbODM\Repository\Exception\PropertyNotFoundException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use spec\Autoprotect\DynamodbODM\Repository\fixtures\NewModel;
use spec\Autoprotect\DynamodbODM\Repository\fixtures\SortKeyModel;
use spec\Autoprotect\DynamodbODM\Repository\fixtures\NewModelNested;

class RepositoryContext implements Context
{
    protected const APPLICATION_ENVIRONMENT_VARIABLE_NAME = 'APP_ENV';

    use TableHelperTrait;
    private DynamoDBRepository $newModelDynamoDbRepository;
    private DynamoDBRepository $sortKeyModelDynamoDbRepository;
    private Hydrator $newModelHydrator;
    private Hydrator $sortKeyModelHydrator;
    protected DocumentRepository $documentRepository;

    protected GetProperty $getPropertyRequestContext;
    protected GetDocument $getDocumentRequestContext;

    protected QueryBuilder $queryBuilder;

    public function __construct()
    {
        include_once __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader('class_exists');

        $this->dynamoDbClient = new DynamoDbClient(array_merge(
            [
                'region' => 'eu-west-2',
                'version' => 'latest',

            ],
            !empty(getenv('DYNAMODB_ENDPOINT_URL', true))
                ? ['endpoint' => getenv('DYNAMODB_ENDPOINT_URL', true)]
                : []
        ));
        $client = new DealTrakDynamoClient($this->dynamoDbClient);
        $marshaler = new Marshaler();
        $this->queryBuilder = new QueryBuilder($marshaler, new ExpressionFactory($marshaler));
        $annotationReader = new AnnotationReader();
        $annotationManager = new AnnotationManager($annotationReader);
        $this->newModelHydrator = new Hydrator(NewModel::class, $annotationManager);
        $this->sortKeyModelHydrator = new Hydrator(SortKeyModel::class, $annotationManager);
        $serializer = new Serializer($annotationManager);

        $this->newModelDynamoDbRepository = new DynamoDBRepository(
            NewModel::class,
            $client,
            $this->queryBuilder,
            $this->newModelHydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );

        $this->sortKeyModelDynamoDbRepository = new DynamoDBRepository(
            SortKeyModel::class,
            $client,
            $this->queryBuilder,
            $this->sortKeyModelHydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );

        $this->documentRepository = new DocumentRepository(
            NewModelNested::class,
            $client,
            $this->queryBuilder,
            $this->newModelHydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );
    }

    /**
     * @BeforeScenario
     * @return void
     */
    public function createTables(): void
    {
        foreach (self::TABLE_SETTINGS as $tableName => $settings) {
            $this->createTableIfNotExists($tableName, $settings);
        }
    }

    /**
     * @AfterScenario
     */
    public function cleanDB(): array
    {
        $marshaler = new Marshaler();

        foreach (self::TABLE_SETTINGS as $tableName => $settings) {
            $queryForScan = $this->queryBuilder
                ->scan($tableName, null)
                ->getQuery();

            $allData = $this->dynamoDbClient->scan($queryForScan);

            $convertedData = array_map(static function ($item) use ($marshaler) {
                return $marshaler->unmarshalItem($item);
            }, $allData['Items']);

            $self = $this;

            array_map(static function ($data) use ($self, $tableName) {
                $itemKey = [
                    'id' => $data['id'],
                ];

                if ($tableName === SortKeyModel::TABLE_NAME) {
                    $itemKey = array_merge($itemKey, [
                        'clientId' => $data['clientId'],
                    ]);
                }

                $queryForDelete = $self->queryBuilder
                    ->deleteItem($tableName)
                    ->itemKey($itemKey)
                    ->getQuery();

                return $self->dynamoDbClient
                    ->deleteItem($queryForDelete);
            }, $convertedData);
        }

        return [];
    }

    /**
     * @Transform /^\[(.*)\]$/
     */
    public function castStringToArray(string $string)
    {
        return explode(', ', $string);
    }

    /**
     * @Then /^i should get "([^"]*)" from database$/
     */
    public function iShouldGetFromDatabase(string $item)
    {
        [$id, $customerName, $customerEmail, $percent] = $this->castStringToArray($item);
        $expectedModel = (new NewModel())
            ->setId($id)
            ->setCustomerName($customerName)
            ->setCustomerEmail($customerEmail)
            ->setPercent((float) $percent);

        $expectedModel
            ->setIndexNameEmail($expectedModel->getIndexNameEmail());

        $foundModel = $this->newModelDynamoDbRepository->get($id);
        TestCase::assertEquals($expectedModel, $foundModel);
    }

    /**
     * @Then /^i should get one "([^"]*)" from database$/
     */
    public function iShouldGetOneFromDatabase(string $item)
    {
        [$id, $customerName, $customerEmail, $percent] = $this->castStringToArray($item);
        $expectedModel = (new NewModel())
            ->setId($id)
            ->setCustomerName($customerName)
            ->setCustomerEmail($customerEmail)
            ->setPercent((float) $percent);

        $expectedModel
            ->setIndexNameEmail($expectedModel->getIndexNameEmail());

        $foundModel = $this->newModelDynamoDbRepository->getOneById($id);
        TestCase::assertEquals($expectedModel, $foundModel);
    }

    /**
     * @Given /^data fixtures:$/
     */
    public function dataFixtures(TableNode $table)
    {
        foreach ($table as $tableRow) {
            $model = (new NewModel())
                ->setCustomerName($tableRow['customerName'])
                ->setCustomerEmail($tableRow['customerEmail'])
                ->setPercent((float) $tableRow['percent'])
                ->setId($tableRow['id']);

            $this->newModelDynamoDbRepository->save($model);
        }
    }

    /**
     * @Given /^data fixtures with sort key:$/
     */
    public function dataFixturesWithSortKey(TableNode $table)
    {
        foreach ($table as $tableRow) {
            $model = (new SortKeyModel())
                ->setClientId($tableRow['clientId'])
                ->setId($tableRow['id']);

            $this->sortKeyModelDynamoDbRepository->save($model);
        }
    }

    /**
     * @Given /^data nested fixtures:$/
     * @param TableNode $table
     */
    public function dataNestedFixtures(TableNode $table)
    {
        foreach ($table as $tableRow) {
            $model = new NewModelNested();

            foreach ($tableRow as $columnName => $columnValue) {
                if (false !== strpos($columnName, "_NESTED")) {
                    $nestedModel = new NewModel();

                    foreach (json_decode($columnValue, true) as $nestedColName => $nestedColValue) {
                        $nestedSetterMethodName = 'set' . ucfirst($nestedColName);
                        $nestedModel->$nestedSetterMethodName($nestedColValue);
                    }

                    $model->setChildObject($nestedModel);
                    continue;
                }
                $setterMethodName = 'set' . ucfirst($columnName);
                $model->$setterMethodName($columnValue);
            }

            $this->newModelDynamoDbRepository->save($model);
        }
    }

    /**
     * @Then /^i should see following items from database:$/
     * @param TableNode $table
     */
    public function iShouldSeeFollowingItemsFromDatabase(TableNode $table)
    {
        $modelCollection = [];
        foreach ($table as $tableRow) {
            $modelCollection[] = (new NewModel())
                ->setId($tableRow['id'])
                ->setCustomerName($tableRow['customerName'])
                ->setCustomerEmail($tableRow['customerEmail'])
                ->setPercent((float) $tableRow['percent']);
        }

        // see if arrays differ
        TestCase::isEmpty(array_udiff(
        // expected records
            $modelCollection,
            // records from the DB
            $this->newModelDynamoDbRepository->getAll(),

            static function (NewModel $expected, NewModel $dbModel): int {
                return strcmp($expected->getId(), $dbModel->getId());
            }
        ));
    }

    /**
     * @Then /^using projection expressions I should see following items from database:$/
     * @param TableNode $table
     *
     * @throws \Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException
     */
    public function usingProjectionExpressionsIShouldSeeFollowingItemsFromDatabase(TableNode $table)
    {
        $modelCollection = [];

        foreach ($table as $tableRow) {
            $nestedModel = new NewModel();

            foreach (json_decode($tableRow['modelData'], true) as $nestedColName => $nestedColValue) {
                $nestedSetterMethodName = 'set' . ucfirst($nestedColName);
                $nestedModel->$nestedSetterMethodName($nestedColValue);
            }

            $modelCollection[] = $nestedModel;
        }

        $itemsFromDatabase = [];

        foreach ($table as $tableRow) {
            if (!isset($tableRow['projectionExpression'])) {
                throw new \RuntimeException('projectionExpression is not set in behat tests');
            }

            $itemsFromDatabase[] = $this->newModelDynamoDbRepository->getByIdAndProjection(
                $tableRow['id'],
                $tableRow['projectionExpression'],
                NewModelNested::class
            );
        }

        // see if arrays differ
        TestCase::isEmpty(array_udiff(

        // expected records
            $modelCollection,
            // records from the DB
            $itemsFromDatabase,

            static function (NewModel $expected, NewModel $dbModel): int {
                return strcmp($expected->getId(), $dbModel->getId());
            }
        ));
    }

    /**
     * @When /^i update item with params "([^"]*)"$/
     */
    public function iUpdateItemWithParams(string $item)
    {
        [$id, $customerName, $customerEmail, $percent] = $this->castStringToArray($item);

        /** @var NewModel $model */
        $model = $this->newModelDynamoDbRepository->get($id);
        $model->setCustomerName($customerName);

        $this->newModelDynamoDbRepository->save($model);
    }

    /**
     * @When /^i delete "([^"]*)" item$/
     */
    public function iDeleteItem(string $itemId)
    {
        $model = (new NewModel())
            ->setId($itemId);

        $this->newModelDynamoDbRepository->delete($model);
    }

    /**
     * @When /^i save item to database with "([^"]*)" data$/
     */
    public function iSaveItemToDatabaseWithData(string $item)
    {
        [$id, $customerName, $customerEmail, $percent] = $this->castStringToArray($item);

        $model = (new NewModel(new AnnotationManager( null)))
            ->setId($id)
            ->setPercent((float) $percent)
            ->setCustomerName($customerName)
            ->setCustomerEmail($customerEmail);

        $this->newModelDynamoDbRepository->save($model);
    }

    /**
     * @Then /^i should see query result from database:$/
     * @param TableNode $table
     */
    public function iShouldSeeQueryResultFromDatabase(TableNode $table)
    {
        $modelCollection = [];
        $itemsFromDatabase = [];

        foreach ($table as $tableRow) {
            $modelCollection[] = (new SortKeyModel())
                ->setId($tableRow['id'])
                ->setClientId($tableRow['clientId']);

            $itemsFromDatabase[] = $this->sortKeyModelDynamoDbRepository->getByIdAndSortKey(
                $tableRow['id'],
                $tableRow['clientId'],
                true,
            );
        }

        TestCase::isEmpty(array_udiff(
            $modelCollection,
            $itemsFromDatabase,
            static function (SortKeyModel $expected, SortKeyModel $dbModel): int {
                return strcmp($expected->getId(), $dbModel->getId());
            }
        ));
    }

    /**
     * @Then /^i should see query result by partition key "([^"]*)" from database:$/
     * @param string $key
     * @param TableNode $table
     * @throws NothingFoundException
     */
    public function iShouldSeeQueryResultByPartitionKeyFromDatabase(string $key, TableNode $table)
    {
        $modelCollection = [];

        foreach ($table as $tableRow) {
            $modelCollection[] = (new SortKeyModel())
                ->setId($tableRow['id'])
                ->setClientId($tableRow['clientId']);
        }

        $itemsFromDatabase = $this->sortKeyModelDynamoDbRepository->getAllById($key);

        TestCase::isEmpty(array_udiff(
            $modelCollection,
            $itemsFromDatabase,
            static function (SortKeyModel $expected, SortKeyModel $dbModel): int {
                return strcmp($expected->getId(), $dbModel->getId());
            }
        ));
    }

    /**
     * @When /^i update existence item with params by conditions "([^"]*)"$/
     */
    public function iUpdateExistenceItemWithParamsByConditions(string $item)
    {
        [$id, $clientId] = $this->castStringToArray($item);

        $model = (new SortKeyModel())
            ->setId($id)
            ->setClientId($clientId);

        try {
            $this->sortKeyModelDynamoDbRepository->saveByConditions($model);
        } catch (ConditionFailedException $exception) {
            TestCase::throwException($exception);
        }
    }

    /**
     * @When /^i delete existence item with params by conditions "([^"]*)"$/
     */
    public function iDeleteExistenceItemWithParamsByConditions(string $item)
    {
        [$id, $clientId] = $this->castStringToArray($item);

        $model = (new SortKeyModel())
            ->setId($id)
            ->setClientId($clientId);

        $this->sortKeyModelDynamoDbRepository->delete($model);
        TestCase::assertEquals($this->sortKeyModelDynamoDbRepository->delete($model), $model);
    }

    /**
     * @When /^i update non existence item with params by conditions "([^"]*)"$/
     */
    public function iUpdateNonExistenceItemWithParamsByConditions(string $item)
    {
        [$id, $clientId] = $this->castStringToArray($item);

        $model = (new SortKeyModel())
            ->setId($id)
            ->setClientId($clientId);

        $createdModel = $this->sortKeyModelDynamoDbRepository->saveByConditions($model);

        TestCase::assertEquals($createdModel, $model);
    }

    /**
     * @Then /^i should see following items from index "([^"]*)" where primary key is "([^"]*)":$/
     *
     * @param string $indexName
     * @param string $primaryKeyValue
     * @param TableNode $table
     *
     * @throws Exception
     */
    public function iShouldSeeFollowingItemsFromIndexWherePrimaryKeyIs(
        string $indexName,
        string $primaryKeyValue,
        TableNode $table
    ) {
        $modelCollection = [];
        foreach ($table as $tableRow) {
            $modelCollection[] = (new NewModel())
                ->setId($tableRow['id'])
                ->setCustomerName($tableRow['customerName'])
                ->setCustomerEmail($tableRow['customerEmail'])
                ->setPercent((float) $tableRow['percent']);
        }

        // see if arrays differ
        TestCase::isEmpty(array_udiff(
        // expected records
            $modelCollection,
            // records from the DB
            $this->newModelDynamoDbRepository->getAllFromIndex(
                $indexName,
                [
                    'indexNameEmail' => $primaryKeyValue
                ]
            ),

            static function (NewModel $expected, NewModel $dbModel): int {
                return strcmp($expected->getId(), $dbModel->getId());
            }
        ));
    }

    /**
     * @Then /^i should see following items from index "([^"]*)" where primary key is "([^"]*)" and id begins with "([^"]*)":$/
     *
     * @param string $indexName
     * @param string $primaryKeyValue
     * @param string $beginWithString
     * @param TableNode $table
     *
     * @throws Exception
     */
    public function iShouldSeeFollowingItemsFromIndexWherePrimaryKeyIsANdBeginsWith(
        string $indexName,
        string $primaryKeyValue,
        string $beginWithString,
        TableNode $table
    ) {
        $modelCollection = [];
        foreach ($table as $tableRow) {
            $modelCollection[] = (new NewModel())
                ->setId($tableRow['id'])
                ->setCustomerName($tableRow['customerName'])
                ->setCustomerEmail($tableRow['customerEmail'])
                ->setIndexNameEmail($primaryKeyValue)
                ->setPercent((float) $tableRow['percent']);
        }

        TestCase::assertEquals($modelCollection, $actual = $this->newModelDynamoDbRepository->getAllFromIndex(
            $indexName,
            [
                'indexNameEmail' => $primaryKeyValue,
                'id' => [
                    'value' => $beginWithString,
                    'type' => QueryBuilderInterface::KEY_CONDITION_EXPRESSION_BEGINS_WITH,
                    'operator' => QueryBuilderInterface::OPERATOR_AND,
                ],
            ]
        ));
    }

    /**
     * @When /^i upsert the collection "([^"]*)" for "([^"]*)" data$/
     */
    public function iUpsertTheCollectionForData(string $property, string $item)
    {
        [$id, $customerName, $customerEmail, $percent] = $this->castStringToArray($item);

        $model = (new NewModel(new AnnotationManager(null)))
            ->setId($id)
            ->setPercent((float) $percent)
            ->setCustomerName($customerName)
            ->setCustomerEmail($customerEmail);

        $this->newModelDynamoDbRepository->save($model);

        $this->newModelDynamoDbRepository->upsertMap($id, $property);
    }

    /**
     * @Then /^i should get "([^"]*)" from "([^"]*)" for item with key "([^"]*)" from database$/
     */
    public function iShouldGetFromForItemWithKeyFromDatabase(int $count, $mapName, $key)
    {
        $foundModel = $this->newModelDynamoDbRepository->get($key);

        TestCase::assertCount($count, $foundModel->{$mapName});
    }

    /**
     * @When I send a get document property request with the key value :keyValue and expression :projection
     */
    public function iSendAGetDocumentPropertyQuestWithKeyAndValueWithExpression(
        string $keyValue,
        string $projection
    ) {
        $this->getPropertyRequestContext = $this->documentRepository->getDocumentProperty()
            ->setConsistentRead(true)
            ->withAttrPath($projection)
            ->withPrKey($keyValue)
        ;
    }

    /**
     * @Then I should see the property with the value :propertyValue
     */
    public function iShouldSeeThePropertyWithTheValue(string $propertyValue)
    {
        TestCase::assertEquals($propertyValue, $this->getPropertyRequestContext->execute());
    }

    /**
     * @Then I should get a PropertyNotFoundException exception
     */
    public function iShouldGetAException()
    {
        try {
            $this->getPropertyRequestContext->execute();
        } catch (PropertyNotFoundException) {

        }
    }

    /**
     * @Then I should get a EntityNotFoundException exception
     */
    public function iShouldGetAEntitynotfoundexceptionException()
    {
        try {
            if (!isset($this->getPropertyRequestContext) && !isset($this->getDocumentRequestContext)) {
                throw new RuntimeException("Document or document property request context should be defined");
            }

            if (isset($this->getPropertyRequestContext)) {
                $this->getPropertyRequestContext->execute();
            }

            if (isset($this->getDocumentRequestContext)) {
                $this->getDocumentRequestContext->execute();
            }

        } catch (EntityNotFoundException) {

        }
    }

    /**
     * @When I send a get document request with the key value :keyValue and expression :projectionExpression
     */
    public function iSendAGetDocumentRequestWithKeyAndValueWithExpression(string $keyValue, string $projectionExpression)
    {
        $this->getDocumentRequestContext = $this->documentRepository->getDocument()
            ->setConsistentRead(true)
            ->withAttrPath($projectionExpression)
            ->withPrKey($keyValue)
        ;
    }

    /**
     * @Then I should see the object coming from database
     */
    public function iShouldSeeTheObjectComingFromDatabase()
    {
        TestCase::assertInstanceOf(NewModel::class, $this->getDocumentRequestContext->execute());
    }

    protected const TABLE_SETTINGS = [
        NewModel::TABLE_NAME => [
            "AttributeDefinitions" => [
                [
                    "AttributeName" => "id",
                    "AttributeType" => "S"
                ],
                [
                    "AttributeName" => "indexNameEmail",
                    "AttributeType" => "S"
                ]
            ],
            "KeySchema" => [
                [
                    "AttributeName" => "id",
                    "KeyType" => "HASH"
                ]
            ],
            "BillingMode" => "PAY_PER_REQUEST",
            "GlobalSecondaryIndexes" => [
                [
                    "IndexName" => "dynamo-db-test-index",
                    "KeySchema" => [
                        [
                            "AttributeName" => "indexNameEmail",
                            "KeyType" => "HASH"
                        ]
                    ],
                    "Projection" => [
                        "ProjectionType" => "ALL"
                    ],
                ],
                [
                    "IndexName" => "indexNameEmail-id-index",
                    "KeySchema" => [
                        [
                            "AttributeName" => "indexNameEmail",
                            "KeyType" => "HASH"
                        ],
                        [
                            "AttributeName" => "id",
                            "KeyType" => "RANGE"
                        ]
                    ],
                    "Projection" => [
                        "ProjectionType" => "ALL"
                    ],
                ]
            ],
        ],
        SortKeyModel::TABLE_NAME => [
            "AttributeDefinitions" => [
                [
                    "AttributeName" => "clientId",
                    "AttributeType" => "S"
                ],
                [
                    "AttributeName" => "id",
                    "AttributeType" => "S"
                ]
            ],
            "KeySchema" => [
                [
                    "AttributeName" => "id",
                    "KeyType" => "HASH"
                ],
                [
                    "AttributeName" => "clientId",
                    "KeyType" => "RANGE"
                ]
            ],
            "BillingMode" => "PAY_PER_REQUEST",
        ],
    ];
}
