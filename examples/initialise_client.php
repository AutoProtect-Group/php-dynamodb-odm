<?php

declare(strict_types=1);

use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Client\DynamodbOperationsClient;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\Serializer\Serializer;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Doctrine\Common\Annotations\AnnotationReader;

// Init native AWS dynamo Db client
$dynamoDbClient = new DynamoDbClient(array_merge(
    [
        'region' => 'eu-west-2',
        'version' => 'latest',
    ]
));

// init lib operations client
$client = new DynamodbOperationsClient($dynamoDbClient);

// native marshaller
$marshaler = new Marshaler();

// query builder
$queryBuilder = new QueryBuilder($marshaler, new ExpressionFactory($marshaler));

// annotation reader
$annotationReader = new AnnotationReader();

// annotation manager
$annotationManager = new AnnotationManager($annotationReader);

// various hydrators
$newModelHydrator = new Hydrator(NewModel::class, $annotationManager);
$sortKeyModelHydrator = new Hydrator(SortKeyModel::class, $annotationManager);

// serializer for
$serializer = new Serializer($annotationManager);
