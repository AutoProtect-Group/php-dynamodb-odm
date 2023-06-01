<?php

namespace Autoprotect\DynamodbODM\Repository;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Annotation\Exception\NoPropertyFoundException;
use Autoprotect\DynamodbODM\Annotation\ModelAnnotationProcessor;
use Autoprotect\DynamodbODM\Annotation\Types\DateType;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Hydrator\HydratorInterface;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Model\Serializer\SerializerInterface;
use Autoprotect\DynamodbODM\Query\QueryBuilderInterface;
use Autoprotect\DynamodbODM\Client\PDOClientInterface;
use Autoprotect\DynamodbODM\Repository\Exception\GetterDoesNotExistException;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Class CommonRepository
 *
 * @package Autoprotect\DynamodbODM\Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected string $modelClassName;

    /**
     * @var HydratorInterface
     */
    protected HydratorInterface $hydrator;

    /**
     * @var PDOClientInterface
     */
    protected PDOClientInterface $client;

    /**
     * @var QueryBuilderInterface
     */
    protected QueryBuilderInterface $queryBuilder;

    /**
     * @var AnnotationManager|null
     */
    protected AnnotationManager $annotationManager;

    /**
     * @var Marshaler
     */
    protected Marshaler $marshaler;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param string $modelClassName
     * @param PDOClientInterface $client
     * @param QueryBuilderInterface $queryBuilder
     * @param HydratorInterface $hydrator
     * @param AnnotationManager|null $annotationManager
     * @param Marshaler|null $marshaler
     * @param SerializerInterface $modelSerializer
     */
    public function __construct(
        string $modelClassName,
        PDOClientInterface $client,
        QueryBuilderInterface $queryBuilder,
        HydratorInterface $hydrator = null,
        AnnotationManager $annotationManager = null,
        Marshaler $marshaler = null,
        SerializerInterface $serializer
    ) {
        $this->modelClassName = $modelClassName;
        $this->client = $client;
        $this->queryBuilder = $queryBuilder;
        $this->annotationManager = $annotationManager
            ?? new AnnotationManager();
        $this->hydrator = $hydrator ?? new Hydrator($modelClassName, $annotationManager);
        $this->marshaler = $marshaler ?? new Marshaler();
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function getAll(): array;

    /**
     * {@inheritDoc}
     */
    abstract public function delete(ModelInterface $model): ModelInterface;

    /**
     * {@inheritDoc}
     */
    abstract public function get(string $id): ?ModelInterface;

    /**
     * {@inheritDoc}
     */
    abstract public function save(ModelInterface $model): ModelInterface;

    /**
     * {@inheritDoc}
     */
    abstract public function saveAsync(ModelInterface $model): PromiseInterface;

    /**
     * {@inheritDoc}
     */
    abstract public function saveByConditions(ModelInterface $model): ?ModelInterface;

    /**
     * This method prepares the data for hydrate process and returns a collection
     *
     * @param array $output
     *
     * @throws \Exception
     * @return array
     *
     */
    protected function prepareData(array $output): array
    {
        $marshaler = new Marshaler();
        $hydratedItems = [];

        foreach ($output as $item) {
            $unmarshaled = $marshaler->unmarshalItem($item);
            $hydratedItems[] = $this->hydrator->hydrate($unmarshaled);
        }

        return $hydratedItems;
    }

    /**
     * @param ModelInterface $model
     *
     * @throws GetterDoesNotExistException
     * @return string
     */
    protected function getModelPrimaryKeyGetter(ModelInterface $model): string
    {
        $primaryKeyGetter = 'get' .
            ucfirst($this->annotationManager->getPrimaryKeyByModelClassName($this->modelClassName));

        if (!method_exists($model, $primaryKeyGetter)) {
            throw new GetterDoesNotExistException(
                sprintf("Primary key getter does not exists for the model class %s", $model::class)
            );
        }

        return $primaryKeyGetter;
    }

    /**
     * @param ModelInterface $model
     *
     * @throws GetterDoesNotExistException
     * @return string
     */
    protected function getModelSortKeyGetter(ModelInterface $model): string
    {
        $sortKeyGetter = 'get' .
            ucfirst($this->annotationManager->getSortKeyByModelClassName($this->modelClassName)->getName());

        if (!method_exists($model, $sortKeyGetter)) {
            throw new GetterDoesNotExistException(
                sprintf("Sort key getter does not exists for the model class %s", $model::class)
            );
        }

        return $sortKeyGetter;
    }

    /**
     * @param ModelInterface $model
     * @throws GetterDoesNotExistException
     * @return array
     */
    protected function prepareModelKey(ModelInterface $model): array
    {
        $primaryKeyGetter = $this->getModelPrimaryKeyGetter($model);

        try {
            $sortKeyGetter = $this->getModelSortKeyGetter($model);
        } catch (GetterDoesNotExistException | NoPropertyFoundException) {
            return $this->buildPrimaryKey($model->$primaryKeyGetter());
        }

        $sortKeyValue = $model->$sortKeyGetter();
        if ($sortKeyValue instanceof \DateTime) {
            $sortKeyValue = $sortKeyValue->format(DateType::DEFAULT_DATETIME_FORMAT);
        }

        return $this->buildPrimaryKey($model->$primaryKeyGetter(), $sortKeyValue);
    }

    protected function buildPrimaryKey(string $partitionKey, $sortKey = null): array
    {
        $modelKey = [];
        $modelKey[$this->annotationManager->getPrimaryKeyByModelClassName($this->modelClassName)]
            = $partitionKey;

        if ($sortKey !== null &&
            $sortKeyName = $this->annotationManager->getSortKeyByModelClassName($this->modelClassName)->getName()
        ) {
            $modelKey[$sortKeyName] = $sortKey;
        }

        return $modelKey;
    }

    protected function getPrimaryPartitionKey(ModelInterface $model)
    {
        $primaryKeyGetter = $this->getModelPrimaryKeyGetter($model);

        return $model->$primaryKeyGetter();
    }
}
