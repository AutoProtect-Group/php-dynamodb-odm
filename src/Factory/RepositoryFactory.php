<?php

namespace Autoprotect\DynamodbODM\Factory;

use Aws\DynamoDb\Marshaler;
use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Client\DealTrakDynamoClient;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Date\DateTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Enum\EnumTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Float\FloatTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\HashMap\HashMapTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Integer\IntegerTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Model\ModelTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ModelCollection\ModelCollectionTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\Money\MoneyTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\ScalarCollection\ScalarCollectionTypeHydratorInterface;
use Autoprotect\DynamodbODM\Hydrator\TypeHydrator\String\StringTypeHydratorInterface;
use Autoprotect\DynamodbODM\Model\Encryptor\EncryptorInterface;
use Autoprotect\DynamodbODM\Repository\DocumentRepository\DocumentRepository;
use Autoprotect\DynamodbODM\Hydrator\Hydrator;
use Autoprotect\DynamodbODM\Model\Serializer\Serializer;
use Autoprotect\DynamodbODM\Query\Factory\ExpressionFactory;
use Autoprotect\DynamodbODM\Query\QueryBuilder;
use Autoprotect\DynamodbODM\Repository\DynamoDBRepository;

/**
 * Class RepositoryFactory
 *
 * @package Autoprotect\DynamodbODM\Factory
 */
class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        protected DealTrakDynamoClient $dealTrakDynamoClient,
        protected AnnotationManager $annotationManager,
        protected Serializer $serializer,
        protected ?Marshaler $marshaler = null,
        ?ExpressionFactory $expressionFactory = null,
        protected ?QueryBuilder $queryBuilder = null,
        protected ?EncryptorInterface $encryptor = null,
        protected ?ModelCollectionTypeHydratorInterface $modelCollectionTypeHydrator = null,
        protected ?ModelTypeHydratorInterface $modelTypeHydrator = null,
        protected ?ScalarCollectionTypeHydratorInterface $scalarCollectionTypeHydrator = null,
        protected ?HashMapTypeHydratorInterface $hashMapTypeHydrator = null,
        protected ?MoneyTypeHydratorInterface $moneyTypeHydrator = null,
        protected ?IntegerTypeHydratorInterface $integerTypeHydrator = null,
        protected ?FloatTypeHydratorInterface $floatTypeHydrator = null,
        protected ?DateTypeHydratorInterface $dateTypeHydrator = null,
        protected ?EnumTypeHydratorInterface $enumTypeHydrator = null,
        protected ?StringTypeHydratorInterface $stringTypeHydrator = null,
    ) {
        $this->marshaler ??= new Marshaler();
        $this->queryBuilder ??= new QueryBuilder(
            $this->marshaler,
            $expressionFactory ?? new ExpressionFactory($this->marshaler)
        );
    }

    /**
     * @param string $modelClassName
     *
     * @return DynamoDBRepository
     */
    public function createRepository(string $modelClassName): DynamoDBRepository
    {
        $hydrator = $this->createHydrator($modelClassName);

        return $this->createAnonymousRepository($modelClassName, $hydrator);
    }

    /**
     * @param string $modelClassName
     *
     * @return DocumentRepository
     */
    public function createDocumentRepository(string $modelClassName): DocumentRepository
    {
        $hydrator = $this->createHydrator($modelClassName);

        return new DocumentRepository(
            $modelClassName,
            $this->dealTrakDynamoClient,
            $this->queryBuilder,
            $hydrator,
            $this->annotationManager,
            $this->marshaler,
            $this->serializer
        );
    }

    /**
     * @param string $className
     *
     * @return Hydrator
     */
    public function createHydrator(string $className): Hydrator
    {
        return new Hydrator(
            $className,
            $this->annotationManager,
            $this->encryptor,
            modelTypeHydrator: $this->modelTypeHydrator,
            modelCollectionTypeHydrator: $this->modelCollectionTypeHydrator,
            scalarCollectionTypeHydrator: $this->scalarCollectionTypeHydrator,
            hashMapTypeHydrator: $this->hashMapTypeHydrator,
            moneyTypeHydrator: $this->moneyTypeHydrator,
            integerTypeHydrator: $this->integerTypeHydrator,
            floatTypeHydrator: $this->floatTypeHydrator,
            dateTypeHydrator: $this->dateTypeHydrator,
            enumTypeHydrator: $this->enumTypeHydrator,
            stringTypeHydrator: $this->stringTypeHydrator,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $repositoryClassName, string $modelClassName): DynamoDBRepository
    {
        $hydrator = $this->createHydrator($modelClassName);

        if (class_exists($repositoryClassName)) {
            return new $repositoryClassName(
                $modelClassName,
                $this->dealTrakDynamoClient,
                $this->queryBuilder,
                $hydrator,
                $this->annotationManager,
                $this->marshaler,
                $this->serializer
            );
        }

        return $this->createAnonymousRepository($modelClassName, $hydrator);
    }

    /**
     * @param string   $modelClassName
     * @param Hydrator $hydrator
     *
     * @return DynamoDBRepository
     */
    private function createAnonymousRepository(string $modelClassName, Hydrator $hydrator): DynamoDBRepository
    {
        return new class(
            $modelClassName,
            $this->dealTrakDynamoClient,
            $this->queryBuilder,
            $hydrator,
            $this->annotationManager,
            $this->marshaler,
            $this->serializer
        ) extends DynamoDBRepository {
            /**
             * {@inheritDoc}
             */
            public function __construct(
                string $modelClassName,
                DealTrakDynamoClient $client,
                QueryBuilder $queryBuilder,
                Hydrator $hydrator,
                AnnotationManager $annotationManager,
                Marshaler $marshaler,
                Serializer $serializer
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
        };
    }
}
