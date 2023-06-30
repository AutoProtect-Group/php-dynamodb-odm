# AWS Dynamodb ODM for PHP

![Code style, unit and functional tests](https://github.com/AutoProtect-Group/php-dynamodb-odm/actions/workflows/ci.yml/badge.svg)

This is a library and an Object Document Mapper to use with AWS DynamoDB in a more convenient way.

## Usage

### Configure the ODM

Set up native client:

```php
$dynamoDbClient = new DynamoDbClient(array_merge(
    [
        'region' => 'eu-west-2',
        'version' => 'latest',
    ]
));
```

Set up the main operations lib client:

```php
$client = new DynamodbOperationsClient($dynamoDbClient);
```

Set up the marshaller. Native AWS marshaller may be taken:
```php
$marshaler = new Marshaler();
```
Set up the Query builder:

```php
$queryBuilder = new QueryBuilder($marshaler, new ExpressionFactory($marshaler));
```

Set up annotation reader and annotation manager:

```php
// annotation reader
$annotationReader = new AnnotationReader();

// annotation manager
$annotationManager = new AnnotationManager($annotationReader);
```

The hydrators for the models:

```php
$newModelHydrator = new Hydrator(NewModel::class, $annotationManager);
$sortKeyModelHydrator = new Hydrator(SortKeyModel::class, $annotationManager);
```
Serializer for inserting records into DB:

```php
// serializer for
$serializer = new Serializer($annotationManager);
```

The full example is in [here](examples/initialise_client.php).

### Model

#### Model field types

The lib operates with models. Each model may have various supported field types. Here is a list of types which correlate with PHP and Dynamodb types:

- `BooleanType`: boolean for DynamoDb and for php
- `CollectionType`: list for DynamoDb, in php it's an array list of items of specific model
- `DateType`: string for DynamoDb, DateTime for php
- `EnumType`: string for DynamoDb, enum for php
- `FloatType`: number for DynamoDb, float for php
- `HashMapType`: map for DynamoDb, in php it's associative array of items of specific model
- `IntegerType`:  number for DynamoDb, int for php
- `ModelType`: map for DynamoDb, instance of model for php
- `Money`: map for DynamoDb, special MoneyObject for PHP. [Money value](https://martinfowler.com/eaaCatalog/money.html) as a concept
- `NumberType`: number for DynamoDb. An abstract type, not a handy one. My be used occasionally
- `ScalarCollectionType`: map for DynamoDb. in php it's associative array of any dynamodb compatible types excluding `CollectionType`, `HashMapType` or `ModelType`
- `StringType`: string for DynamoDb and for php

Here is a model example:

```php
class ExampleDemoModel extends Model
{
    protected const TABLE_NAME = 'test-table';
    
    // Primary means that this is a partition key for the DynamoDb table
    #[StringType, Primary]  protected string $id;
    #[StringType]           protected string $name;
    #[FloatType]            protected float $price;
    #[Money]                protected Money $priceNet;
    #[FloatType]            protected float $percent;
    #[IntegerType]          protected int $itemsAmount;
    #[DateType]             protected DateTime $createdAt;
    #[BooleanType]          protected bool $isDeleted;
    #[BooleanType]          protected bool $isPhoneNumber;
    #[ModelType([ModelType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected RelatedModel $buyer;
    #[CollectionType([CollectionType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected array $buyers;
    #[ModelType([Asset::MODEL_CLASS_NAME => Asset::class])]
    protected Asset $asset;
    #[HashMapType([HashMapType::MODEL_CLASS_NAME => RelatedModel::class])]
    protected array $buyersMap;
    
    // getter and setters should be here    
 }
```
Model full example is here: [model.php](examples/model.php)

#### Enumerations example

Enumerations are also supported. Here is an example of the model with enumeration fields:

```php
class ModelWithEnumeration extends Model
{
    #[Primary, StringType]
    protected string $id;

    #[EnumType]
    protected OrderStatus $orderStatus;
    
    // union types
    #[EnumType]
    protected OrderStatus|ApplicationStatus $unionStatus;

    // union types with null
    #[EnumType]
    protected OrderStatus|ApplicationStatus|null $unionNullableStatus;

    // isStrict means the value will be null in case wrong value comes from the DB
    #[EnumType(isStrict: false)]
    protected ?OrderStatus $orderStatusAdditional = null;

    #[EnumType]
    protected CustomerType $customerType;
}
```

#### Fields encryption

Certain types custom encryption is supported. In case there are some fields which needs to be encrypted.

First of all we need to create a custom encryptor:

```php
MyEncryptor implements EncryptorInterface {
    protected const ENCRYPTION_KEY = 'def000008053addc0f94b14c0e480a10631a0a970b3565e5a7a2aeaeeb51a39e2d139a8977bc02be0195f0036a29aefff9df6d2ddb81432d14b4dce82b83b3a95c6d0205';

    public function decrypt(string|array $encryptedData, array $options = []): string|array
    {
        // any decryption way may be implemented
        if (is_array($encryptedData)) {
            // ...specific property decryption operations...
            return $encryptedData;
        }
        
        return Crypto::decrypt(
            $encryptedData,
            Key::loadFromAsciiSafeString(static::ENCRYPTION_KEY)
        );
    }
}
```

Then the decryptor should be passed into the hydrator:

```php
$newModelHydrator = new Hydrator(
    EncryptionDemoModel::class,
    $annotationManager,
    new MyEncryptor(),
);
```
And the model may be the following:

```php
class EncryptionDemoModel extends Model
{
    #[Key\Primary, Types\StringType]
    protected string $id;

    // ability to encrypt a specific property in a scalar associative  array 
    #[Types\ScalarCollectionType, Encrypted(["encryptedProperty" => "secretProperty"])]
    protected array $encryptedArray;

    #[Types\StringType, Encrypted]
    protected string $encryptedName;
}
```

### Set up the repository for your model

The best way to operate with records is to create a repository. There is a built-in already:

```php
$newModelDynamoDbRepository = new DynamoDBRepository(
    NewModel::class,
    $client,
    $queryBuilder,
    $newModelHydrator,
    $annotationManager,
    $marshaler,
    $serializer
);
```
There are built-in operation in the default repository.

#### Get model by partition Id

##### Just by partition key 
```php
$foundModel = $newModelDynamoDbRepository->get($id);
```

##### By partition key and sort key
```php
$foundModel = $newModelDynamoDbRepository->get($id, $sortKey);
```

##### Non-consistent read
```php
$foundModel = $newModelDynamoDbRepository->get($id, $sortKey, false);
```

##### Get one item
```php
$foundModel = $newModelDynamoDbRepository->getOneById($id, $sortKey, false);
```

#### Insert model
```php
$newModelDynamoDbRepository->save($model);
```

#### Delete item
```php
$newModelDynamoDbRepository->delete($model);
```

### Document repository

Sometimes we need to fetch not the whole model, but just a part of it. For this purpose there is such called `DocumentRepository`. The part of the document may be technically fetched using native DynamoDb projection expressions.

Setting `DocumentRepository` up:

```php
$documentRepository = new DocumentRepository(
    NewModelNested::class,
    $client,
    $this->queryBuilder,
    $this->newModelHydrator,
    $annotationManager,
    $marshaler,
    $serializer
);
```

#### Get a model by projection expression

```php

$projectionExpression = "property.subPropertyModel";

$model = $documentRepository->getDocument()
    ->setConsistentRead(true)
    ->withAttrPath($projectionExpression)
    ->withPrKey($keyValue)
    ->execute()
;
```

#### Get a specific scalar property by projection expression

```php
$projectionExpression = "property.subPropertyModel.name";

$name = $this->documentRepository->getDocumentProperty()
    ->setConsistentRead(true)
    ->withAttrPath($projection)
    ->withPrKey($keyValue)
    ->execute()
;
```

#### Get/create/update/delete operations

Document repository supports specific property get/create/update/delete operations:

- `createDocument()`
- `updateDocument()`
- `removeDocument()`
- `getDocumentCollection()`
- `updateDocumentCollection()`
- `createDocumentCollection()`

### Query builder

Another powerful feature is query builders. This adds flexibility to fetch items by specific criteria which is supported by DynamoDB.

This is a way to work with the Dynamodb using raw queries and results

#### Get query builder

Fetch items:

```php
$getItemQuery = $queryBuilder
    ->getItem(self::DB_TABLE)
    ->itemKey([$itemKey => $keyValue])
    ->getQuery();
    
$item = $this->dynamoDbClient
    ->getItem($getItemQuery)->get('Item');
```
#### Update query builder

Ability to update specific attributes.

```php
$attributesForUpdate = [
   "numberProp" => 2, 
   "stringProp" => "updated string value", 
   "hashMapProp.map-id-1.type" => "updated map-type-1", 
   "hashMapProp.map-id-1.mapProp" => "updated mapProp", 
   "listProp" => [
         "updated listProp 1", 
         "updated listProp 2" 
      ] 
]; 

$getItemQuery = $queryBuilder
    ->updateItem(self::DB_TABLE)
    ->itemKey([$itemKey => $keyValue])
    ->attributes($attributesForUpdate)
    ->getQuery();

$dynamoDbClient->updateItem($getItemQuery);
```

## Local dev environment installation

1. In order to build a dev image, please, run: 
```bash
docker-compose build
```
2. Then run to install dependencies: 
```bash
docker-compose run --no-deps dynamodb-odm composer install
```

## Running tests

### Unit tests

This package uses phpspec for running unit tests.

Run them using the following way:
```bash
docker-compose run --no-deps dynamodb-odm vendor/bin/phpspec run
```

One can use environment variables in the `.env.local` file to be able to debug the library. For this just Copy file [.env.local.sample](.env.local.sample) into [.env.local](.env.local) and set up the variable according to your OS.

And then run the tests with:

```bash
docker-compose --env-file ./.env.local run  --no-deps dynamodb-odm vendor/bin/phpspec run
```

### Functional tests

This package uses behat for running functional tests.
 
Then just run the tests:
 
```bash
docker-compose run dynamodb-odm vendor/bin/behat -c behat.yml --stop-on-failure
```

### Syntax check tests

You need to check if the code style is OK by running:
```bash
docker-compose run --no-deps dynamodb-odm vendor/bin/phpcs --standard=/application/phpcs.xml
```
