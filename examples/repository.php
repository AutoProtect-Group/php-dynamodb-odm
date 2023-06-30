<?php

declare(strict_types=1);

use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

// all variables are taken from the initialize client
$newModelDynamoDbRepository = new DynamoDBRepository(
    NewModel::class,
    $client,
    $queryBuilder,
    $newModelHydrator,
    $annotationManager,
    $marshaler,
    $serializer
);