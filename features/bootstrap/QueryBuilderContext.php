<?php

declare(strict_types=1);

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\OperationBuilder\Transact\TransactUpdateQuery;
use Autoprotect\DynamodbODM\Query\OperationBuilder\UpdateItemBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;


/**
 * Class QueryBuilderContext
 */
class QueryBuilderContext implements Context
{
    use TableHelperTrait;
    public const DB_TABLE = 'test-db-adapter-dev';

    protected Marshaler $marshaler;

    protected QueryBuilder $queryBuilder;

    protected array $item;

    public function __construct()
    {
        $this->marshaler = new Marshaler();
        $this->queryBuilder = new QueryBuilder(
            $this->marshaler,
            new ExpressionFactory($this->marshaler)
        );
        $this->dynamoDbClient = new DynamoDbClient(array_merge(
            [
                'region' => 'eu-west-2',
                'version' => 'latest',

            ],
            !empty(getenv('DYNAMODB_ENDPOINT_URL', true))
                ? ['endpoint' => getenv('DYNAMODB_ENDPOINT_URL', true)]
                : []
        ));
    }

    /**
     * @BeforeScenario
     * @return void
     */
    public function createTable(): void
    {
        $this->createTableIfNotExists(
            static::DB_TABLE,
            [
                "AttributeDefinitions" => [
                    [
                        "AttributeName" => "id",
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
            ]
        );
    }

    /**
     * @Given there is an item in DB:
     *
     * @param PyStringNode $jsonItem
     */
    public function thereIsAnItemInDb(PyStringNode $jsonItem): void
    {
        $this->iSendAPutItemRequestWithBody($jsonItem);
    }

    /**
     * @Given there are the items in DB:
     *
     * @param PyStringNode $jsonItem
     */
    public function thereAreTheItemsInDb(PyStringNode $jsonItem): void
    {
        $marshaledItems = $this->jsonDecode($jsonItem->getRaw());

        foreach ($marshaledItems as $marshaledItem) {
            $putItemQuery = $this->queryBuilder
                ->putItem(self::DB_TABLE)
                ->itemData($marshaledItem)
                ->getQuery();

            $this->dynamoDbClient
                ->putItem($putItemQuery);
        }
    }

    /**
     * @When I send a put item request with body:
     *
     * @param PyStringNode $jsonItem
     */
    public function iSendAPutItemRequestWithBody(PyStringNode $jsonItem): void
    {
        $marshaledItem = $this->jsonDecode($jsonItem->getRaw());

        $putItemQuery = $this->queryBuilder
            ->putItem(self::DB_TABLE)
            ->itemData($marshaledItem)
            ->getQuery();

        $this->dynamoDbClient
            ->putItem($putItemQuery);
    }

    /**
     * @When I send a transact write items request with body:
     *
     * @param PyStringNode $jsonItem
     *
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function iSendATransactWriteItemsRequestWithBody(PyStringNode $jsonItem): void
    {
        $marshaledItem = $this->jsonDecode($jsonItem->getRaw());
        $id = $marshaledItem['id'];
        unset($marshaledItem['id']);

        $transactUpdateQuery = new TransactUpdateQuery(
            self::DB_TABLE,
            ['id' => $id],
            $marshaledItem,
            new UpdateItemBuilder(new Marshaler(), self::DB_TABLE)
        );

        $transactWriteQuery = $this->queryBuilder
            ->transactWriteItem()
            ->addTransaction($transactUpdateQuery)
            ->getQuery();

        $this->dynamoDbClient->transactWriteItems($transactWriteQuery);
    }

    /**
     * @When I send a get item request with key :itemKey and value :keyValue
     *
     * @param string $itemKey
     * @param string $keyValue
     */
    public function iSendAGetItemRequestWithId(string $itemKey, string $keyValue): void
    {
        $getItemQuery = $this->queryBuilder
            ->getItem(self::DB_TABLE)
            ->itemKey([$itemKey => $keyValue])
            ->getQuery();

        $this->item = $this->dynamoDbClient
            ->getItem($getItemQuery)->get('Item');

        if ($this->item === null) {
            throw new ExpectationFailedException(
                sprintf('Item with id "%s" not found in DB "%s".', $itemKey, self::DB_TABLE)
            );
        }
    }

    /**
     * @When I send update item request with key :arg1 and value :arg2 with attributes:
     *
     * @param string       $itemKey
     * @param string       $keyValue
     * @param PyStringNode $jsonAttributes
     *
     * @throws ReflectionException
     * @throws ExpressionNotFoundException
     */
    public function iSendUpdateItemRequestWithKeyAndValueWithAttributes(
        string $itemKey,
        string $keyValue,
        PyStringNode $jsonAttributes
    ): void {
        $attributes = $this->jsonDecode($jsonAttributes->getRaw());

        $getItemQuery = $this->queryBuilder
            ->updateItem(self::DB_TABLE)
            ->itemKey([$itemKey => $keyValue])
            ->attributes($attributes)
            ->getQuery();

        $this->dynamoDbClient->updateItem($getItemQuery);
    }

    /**
     * @When I send append items request with key :arg1 and value :arg2 with attributes:
     *
     * @param string       $itemKey
     * @param string       $keyValue
     * @param PyStringNode $jsonAttributes
     *
     * @throws ReflectionException
     * @throws ExpressionNotFoundException
     */
    public function iSendAppendItemsRequestWithKeyAndValueWithAttributes(
        string $itemKey,
        string $keyValue,
        PyStringNode $jsonAttributes
    ): void {
        $attributes = $this->jsonDecode($jsonAttributes->getRaw());

        $getItemQuery = $this->queryBuilder
            ->updateItem(self::DB_TABLE)
            ->itemKey([$itemKey => $keyValue])
            ->attributesAppendList($attributes)
            ->getQuery();

        $this->dynamoDbClient->updateItem($getItemQuery);
    }

    /**
     * @When I send remove item attributes request with key :arg1 and value :arg2 with attributes:
     *
     * @param string       $itemKey
     * @param string       $keyValue
     * @param PyStringNode $jsonAttributes
     *
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function iSendRemoveItemAttributesRequestWithKeyAndValueWithAttributes(
        string $itemKey,
        string $keyValue,
        PyStringNode $jsonAttributes
    ): void {
        $attributePaths = $this->jsonDecode($jsonAttributes->getRaw());

        $getItemQuery = $this->queryBuilder
            ->removeItemAttribute(self::DB_TABLE)
            ->itemKey([$itemKey => $keyValue])
            ->removeAttributesByPath($attributePaths)
            ->getQuery();

        $this->dynamoDbClient->updateItem($getItemQuery);
    }


    /**
     * @When I send a get item request with key :itemKey and value :keyValue with expression :expression
     *
     * @param string $itemKey
     * @param string $keyValue
     * @param string $expression
     */
    public function iSendAGetItemRequestWithKeyAndValueWithExpression(
        string $itemKey,
        string $keyValue,
        string $expression
    ) {
        $getItemQuery = $this->queryBuilder
            ->getItem(self::DB_TABLE)
            ->itemKey([$itemKey => $keyValue])
            ->setProjections([$expression])
            ->getQuery();

        $this->item = $this->dynamoDbClient
            ->getItem($getItemQuery)->get('Item');

        if ($this->item === null) {
            throw new ExpectationFailedException(
                sprintf('Item with id "%s" not found in DB "%s".', $itemKey, self::DB_TABLE)
            );
        }
    }

    /**
     * @When I send get items request with limit :limit
     *
     * @param int $limit
     */
    public function iSendGetItemsRequestWithLimit(int $limit)
    {
        $getItemQuery = $this->queryBuilder
            ->scan(self::DB_TABLE, $limit)
            ->getQuery();

        $this->item = $this->dynamoDbClient
            ->scan($getItemQuery)->get('Items');
    }

    /**
     * @Then the un marshaled item body must be:
     *
     * @param PyStringNode $jsonItem
     */
    public function theUnMarshaledItemBodyMustBe(PyStringNode $jsonItem): void
    {
        $expectedItem = $this->jsonDecode($jsonItem->getRaw());
        $actualItem = $this->item;

        Assert::assertEquals($expectedItem, $actualItem);
    }

    /**
     * @Then the marshaled item body must be:
     *
     * @param PyStringNode $jsonItem
     */
    public function theMarshaledItemBodyMustBe(PyStringNode $jsonItem): void
    {
        $expectedItem = $this->jsonDecode($jsonItem->getRaw());
        $actualItem = $this->unmarshalItem($this->item);

        Assert::assertEquals($expectedItem, $actualItem);
    }

    /**
     * @Then the marshaled items body must be:
     *
     * @param PyStringNode $jsonItem
     */
    public function theMarshaledItemsBodyMustBe(PyStringNode $jsonItem): void
    {
        $expectedItem = $this->jsonDecode($jsonItem->getRaw());
        $actualItem = array_map(fn ($item) => $this->unmarshalItem($item), $this->item);

        Assert::assertEquals($expectedItem, $actualItem);
    }

    /**
     * @When there should be not more than :amount items
     */
    public function thereShouldBeNotMoreThanItems(int $amount)
    {
        Assert::assertLessThanOrEqual($amount, count($this->item));
    }

    /**
     * @param string $json
     *
     * @return array
     */
    private function jsonDecode(string $json): array
    {
        return json_decode($json, true);
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function unmarshalItem(array $item): array
    {
        return $this->marshaler
            ->unmarshalItem($item);
    }
}
