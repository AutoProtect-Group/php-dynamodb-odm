<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Repository;

use Autoprotect\DynamodbODM\Annotation\Exception\NoPropertyFoundException;
use stdClass;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Client\PDOClientInterface;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\ModelInterface;
use Autoprotect\DynamodbODM\Model\Serializer\SerializerInterface;
use Autoprotect\DynamodbODM\Query\Expression\Condition\AttributeTypeIs;
use Autoprotect\DynamodbODM\Query\Exception\ExpressionNotFoundException;
use Autoprotect\DynamodbODM\Query\OperationBuilder\GsiQueryBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Autoprotect\DynamodbODM\Query\QueryBuilderInterface;
use Autoprotect\DynamodbODM\Repository\Exception\ConditionFailedException;
use Autoprotect\DynamodbODM\Repository\Exception\EntityNotFoundException;
use Autoprotect\DynamodbODM\Client\DynamodbOperationsClient;
use Autoprotect\DynamodbODM\Repository\Exception\GetterDoesNotExistException;
use Autoprotect\DynamodbODM\Repository\Exception\NothingFoundException;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use ReflectionException;
use Autoprotect\DynamodbODM\Query\Expression\Condition\AttributeNotExistsExpression;

class DynamoDBRepository extends AbstractRepository
{
    protected ?int $limit = null;

    public const SORT_ORDER_DEFAULT = null;
    public const SORT_ORDER_ASC = true;
    public const SORT_ORDER_DESC = false;
    public const KEY_EXPRESSION_VALUE_FIELD = 'value';
    public const KEY_EXPRESSION_OPERATOR_FIELD = 'operator';
    public const KEY_EXPRESSION_TYPE_FIELD = 'type';

    /**
     * {@inheritDoc}
     */
    public function __construct(
        string $modelClassName,
        DynamodbOperationsClient $client,
        QueryBuilder $queryBuilder,
        Hydrator $hydrator,
        AnnotationManager $annotationManager,
        Marshaler $marshaler,
        SerializerInterface $serializer
    ) {
        parent::__construct(
            $modelClassName,
            $client,
            $queryBuilder,
            $hydrator,
            $annotationManager,
            $marshaler,
            $serializer
        );
    }

    /**
     * @return string
     */
    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    /**
     * @return Hydrator
     */
    public function getHydrator(): Hydrator
    {
        return $this->hydrator;
    }

    /**
     * @return DynamodbOperationsClient
     */
    public function getClient(): PDOClientInterface
    {
        return $this->client;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilderInterface
    {
        return $this->queryBuilder;
    }

    /**
     * @return AnnotationManager|null
     */
    public function getAnnotationManager(): ?AnnotationManager
    {
        return $this->annotationManager;
    }

    /**
     * @return Marshaler
     */
    public function getMarshaler(): Marshaler
    {
        return $this->marshaler;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function getAll(): array
    {
        $query = $this->queryBuilder
            ->scan($this->modelClassName::getTableName(), $this->limit)
            ->getQuery();

        $output = $this->client->scan($query);

        return $this->prepareData($output);
    }

    /**
     * @param string $indexName
     * @param array $conditions
     * @param bool|null $sort
     *
     * @throws Exception
     * @return array
     */
    public function getAllFromIndex(string $indexName, array $conditions, ?bool $sort = self::SORT_ORDER_DEFAULT): array
    {
        /** @var GsiQueryBuilder $query */
        $query = $this->queryBuilder
            ->queryIndex($this->modelClassName::getTableName(), $indexName)
            ->setScanIndexForward($sort);

        foreach ($conditions as $key => $value) {
            if (!is_array($value)) {
                $query->addKeyCondition($key, $value);
            } else {
                if (isset($value[self::KEY_EXPRESSION_VALUE_FIELD])) {
                    $query->addKeyCondition(
                        $key,
                        $value[self::KEY_EXPRESSION_VALUE_FIELD],
                        $value[self::KEY_EXPRESSION_OPERATOR_FIELD] ?? QueryBuilderInterface::OPERATOR_AND,
                        $value[self::KEY_EXPRESSION_TYPE_FIELD] ?? null
                    );
                }
            }
        }

        $query = $query->getQuery();

        $output = $this->client->query($query);

        return $this->prepareData($output);
    }

    /**
     * @param int|null $limit
     *
     * @return $this
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param ModelInterface $model
     * @throws GetterDoesNotExistException
     * @return ModelInterface
     */
    public function delete(ModelInterface $model): ModelInterface
    {
        $query = $this->queryBuilder
            ->deleteItem($this->modelClassName::getTableName())
            ->itemKey($this->prepareModelKey($model))
            ->getQuery();

        $this->client->delete($this->getPrimaryPartitionKey($model), $query);

        return $model;
    }

    /**
     * {@inheritDoc}
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function get(string $id, ?string $sortKey = null, bool $consistentRead = true): ?ModelInterface
    {
        $query = $this->queryBuilder
            ->getItem($this->modelClassName::getTableName())
            ->setConsistentRead($consistentRead)
            ->itemKey($this->buildPrimaryKey($id, $sortKey))
            ->getQuery();

        $output = $this->client->get($id, $query);

        if (null === $output) {
            throw new EntityNotFoundException(
                sprintf('Failed to get %s by ID %s and sort key %s', $this->modelClassName, $id, (string) $sortKey)
            );
        }

        return $this->hydrator->hydrate($this->marshaler->unmarshalItem($output));
    }

    /**
     * @param string $id
     * @param string $property
     *
     * @throws ReflectionException
     *
     * @throws ExpressionNotFoundException
     * @return ModelInterface
     */
    public function upsertMap(string $id, string $property)
    {
        $modelClassName = $this->modelClassName;

        $query = $this->getQueryBuilder()
            ->updateItem($modelClassName::getTableName())
            ->itemKey([$this->annotationManager->getPrimaryKeyByModelClassName($modelClassName) => $id])
            ->attributes([
                $property => new stdClass()
            ])
            ->addKeyCondition($property, AttributeNotExistsExpression::class)
            ->addKeyValueCondition(
                $property,
                'M',
                AttributeTypeIs::class,
                QueryBuilderInterface::OPERATOR_OR_NOT
            )
            ->getQuery();

        $output = $this->client->update($id, $query);

        return $output;
    }

    /**
     * Get one table item by a partition key
     *
     * @param string $id
     * @param bool|null $sort
     *
     * @throws EntityNotFoundException
     *
     * @return ModelInterface
     */
    public function getOneById(
        string $id,
        ?bool $sort = self::SORT_ORDER_DEFAULT,
        bool $consistentRead = true
    ): ModelInterface {
        $modelClassName = $this->modelClassName;

        $query = $this->queryBuilder
            ->query($modelClassName::getTableName())
            ->setConsistentRead($consistentRead)
            ->setLimit(1)
            ->setScanIndexForward($sort)
            ->addKeyCondition($this->annotationManager->getPrimaryKeyByModelClassName($modelClassName), $id)
            ->getQuery();

        $output = $this->client->query($query);

        if (empty($output)) {
            throw new EntityNotFoundException(
                sprintf('Failed to get %s by ID %s', $this->modelClassName, $id)
            );
        }

        $result = $this->hydrator->hydrate($this->marshaler->unmarshalItem($output[0]));

        if ((is_object($result)) && (method_exists($result, 'hasExpired')) && ($result->hasExpired())) {
            throw new EntityNotFoundException(
                sprintf('%s by ID %s has expired', $this->modelClassName, $id)
            );
        }

        return $result;
    }

    /**
     * Get all table items by a partition key
     *
     * @param string $id
     * @param bool|null $sort
     * @param array $filterExpression
     *
     * @throws NothingFoundException
     * @return array
     */
    public function getAllById(
        string $id,
        ?bool $sort = self::SORT_ORDER_DEFAULT,
        array $filterExpression = [],
        bool $consistentRead = true
    ): array {
        $modelClassName = $this->modelClassName;

        $query = $this->queryBuilder
            ->query($modelClassName::getTableName())
            ->setConsistentRead($consistentRead)
            ->setScanIndexForward($sort)
            ->addKeyCondition($this->annotationManager->getPrimaryKeyByModelClassName($modelClassName), $id)
            //Add filter conditions to query
            ->addFilterConditions($filterExpression)
            ->getQuery();

        $output = $this->client->query($query);

        if (empty($output)) {
            throw new NothingFoundException();
        }

        return $this->prepareData($output);
    }

    /**
     * Get nested model by id and projections list
     *
     * @param string $id
     * @param string $projection
     * @param string|null $baseModelClassName
     *
     * @throws EntityNotFoundException
     * @throws Exception
     * @return ModelInterface|null
     */
    public function getByIdAndProjection(
        string $id,
        string $projection,
        ?string $baseModelClassName = null,
        bool $consistentRead = true
    ): ?ModelInterface {
        $getItemModelClassName = $baseModelClassName ?? $this->modelClassName;

        $itemQueryBuilder = $this->queryBuilder
            ->getItem($getItemModelClassName::getTableName())
            ->setConsistentRead($consistentRead)
            ->itemKey(
                [$this->annotationManager->getPrimaryKeyByModelClassName($getItemModelClassName) => $id]
            )
            ->setProjections([$projection]);

        $query = $itemQueryBuilder->getQuery();

        $output = $this->client->get($id, $query);

        if (null === $output) {
            throw new EntityNotFoundException(
                sprintf('Failed to get %s by ID %s and projection %s', $getItemModelClassName, $id, $projection)
            );
        }

        return $this->hydrator->hydrate(dot($this->marshaler->unmarshalItem($output))->get($projection));
    }

    /**
     * {@inheritDoc}
     */
    public function save(ModelInterface $model): ModelInterface
    {
        $query = $this->queryBuilder
            ->putItem($this->modelClassName::getTableName())
            ->itemData($this->serializer->serialize($model))
            ->getQuery();

        $this->client->put($query);

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAsync(ModelInterface $model): PromiseInterface
    {
        $query = $this->queryBuilder
            ->putItem($this->modelClassName::getTableName())
            ->itemData($this->serializer->serialize($model))
            ->getQuery();

        return $this->client->putAsync($query);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function update(string $id, array $updateParams): ModelInterface
    {
        $query = $this->queryBuilder
            ->updateItem($this->modelClassName::getTableName())
            ->itemKey([
                $this->annotationManager->getPrimaryKeyByModelClassName($this->modelClassName) => $id,
            ])
            ->attributes($updateParams)
            ->getQuery();

        $output = $this->client->update($id, $query);

        return $this->hydrator->hydrate($this->marshaler->unmarshalItem($output));
    }

    /**
     * @param string $id
     * @param mixed $sortKey
     *
     * @throws Exception
     *
     * @return ModelInterface|null
     */
    public function getByIdAndSortKey(
        string $id,
        $sortKey,
        bool $consistentRead = true
    ): ?ModelInterface {
        $modelClassName = $this->modelClassName;

        $query = $this->queryBuilder
            ->query($modelClassName::getTableName())
            ->setConsistentRead($consistentRead)
            ->addKeyCondition($this->annotationManager->getPrimaryKeyByModelClassName($modelClassName), $id)
            ->addKeyCondition(
                $this->annotationManager->getSortKeyByModelClassName($modelClassName)->getName(),
                $sortKey
            )
            ->getQuery();

        $output = $this->client->query($query);

        if (empty($output)) {
            return null;
        }

        return $this->hydrator->hydrate($this->marshaler->unmarshalItem($output[0]));
    }

    /**
     * {@inheritDoc}
     * @throws ConditionFailedException
     * @throws ExpressionNotFoundException
     * @throws ReflectionException
     */
    public function saveByConditions(ModelInterface $model): ?ModelInterface
    {
        $modelClassName = $this->modelClassName;

        $query = $this->queryBuilder
            ->putItem($this->modelClassName::getTableName())
            ->itemData($this->serializer->serialize($model))
            ->addCondition($this->annotationManager->getPrimaryKeyByModelClassName($modelClassName));

        try {
            $query->addCondition($this->annotationManager->getSortKeyByModelClassName($modelClassName)->getName());
        } catch (NoPropertyFoundException) {
            // Just no sort key for the model which is normal. We then skip it
        }

        $query = $query->getQuery();

        try {
            $this->client->put($query);
        } catch (DynamoDbException $exception) {
            if ($exception->getAwsErrorCode() === ConditionFailedException::AWS_ERROR_CODE) {
                throw new ConditionFailedException($exception->getMessage());
            }

            throw $exception;
        }

        return $model;
    }
}
